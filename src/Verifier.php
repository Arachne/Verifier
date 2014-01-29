<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Arachne\Verifier\Exception\InvalidArgumentException;
use Nette\Application\BadRequestException;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\PresenterComponent;
use Nette\Application\UI\PresenterComponentReflection;
use Nette\Object;
use ReflectionClass;
use ReflectionMethod;
use Reflector;

/**
 * @author Jáchym Toušek
 */
class Verifier extends Object
{

	/** @var IRuleProvider[] */
	protected $ruleProviders;

	/** @var IRuleHandlerLoader */
	protected $handlerLoader;

	/** @var IPresenterFactory */
	protected $presenterFactory;

	public function __construct(array $ruleProviders, IRuleHandlerLoader $handlerLoader, IPresenterFactory $presenterFactory)
	{
		$this->ruleProviders = $ruleProviders;
		$this->handlerLoader = $handlerLoader;
		$this->presenterFactory = $presenterFactory;
	}

	/**
	 * Checks whether the given reflection contains any conditions that are not met.
	 * @param ReflectionClass|ReflectionMethod $rules
	 * @param Request $request
	 * @param string $component
	 * @throws BadRequestException
	 */
	public function checkRules(Reflector $reflection, Request $request, $component = NULL)
	{
		if ($reflection instanceof ReflectionMethod || $reflection instanceof ReflectionClass) {
			$rules = array();
			foreach ($this->ruleProviders as $provider) {
				$rules += $provider->getRules($reflection, $request, $component);
			}
		} else {
			throw new InvalidArgumentException('Reflection must be an instance of either ReflectionMethod or ReflectionClass.');
		}

		foreach ($rules as $rule) {
			if (!$rule instanceof IRule) {
				continue;
			}
			$this->handlerLoader
				->getRuleHandler(get_class($rule))
				->checkRule($rule, $request, $component);
		}
	}

	/**
	 * Checks whether it is possible to run the given request.
	 * @param Request $request
	 * @param PresenterComponent $component
	 * @return bool
	 */
	public function isLinkVerified(Request $request, PresenterComponent $component)
	{
		try {
			$parameters = $request->getParameters();
			if (isset($parameters[Presenter::SIGNAL_KEY])) {
				// No need to check anything else, the requirements for presenter,
				// action and component had to be met for the current request.
				$signalId = $parameters[Presenter::SIGNAL_KEY];
				if (!is_string($signalId)) {
					throw new InvalidArgumentException('Signal name is not a string.');
				}
				$pos = strrpos($signalId, '-');
				if ($pos) {
					// signal for a component
					if ($component->getName() !== substr($signalId, 0, $pos)) {
						throw new InvalidArgumentException("Wrong signal receiver, expected '" . substr($signalId, 0, $pos) . "' component but '{$component->getName()}' was given.");
					}
					$reflection = new PresenterComponentReflection($component);
					$signal = substr($signalId, $pos + 1);
				} else {
					// signal for presenter
					$presenter = $request->getPresenterName();
					$reflection = new PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));
					$signal = $signalId;
				}

				// Signal requirements
				$method = 'handle' . $signal;
				if ($reflection->hasCallableMethod($method)) {
					$this->checkRules($reflection->getMethod($method), $request, $component->getParent() === $component ? NULL : $component->getName());
				}

			} else {
				$presenter = $request->getPresenterName();
				$reflection = new PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));

				// Presenter requirements
				$this->checkRules($reflection, $request);

				// Action requirements
				$action = $parameters[Presenter::ACTION_KEY];
				$method = 'action' . $action;
				if ($reflection->hasCallableMethod($method)) {
					$this->checkRules($reflection->getMethod($method), $request);
				}
				$method = 'render' . $action;
				if ($reflection->hasCallableMethod($method)) {
					$this->checkRules($reflection->getMethod($method), $request);
				}
			}
		} catch (BadRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Checks whether the parent component can create the subcomponent with given name.
	 * @param string $name
	 * @param Request $request
	 * @param PresenterComponent $parent
	 * @return bool
	 */
	public function isComponentVerified($name, Request $request, PresenterComponent $parent)
	{
		$reflection = new PresenterComponentReflection($parent);

		try {
			$method = 'createComponent' . ucfirst($name);
			if ($reflection->hasMethod($method)) {
				$factory = $reflection->getMethod($method);
				$this->checkRules($factory, $request, $parent->getParent() === $parent ? NULL : $parent->getName());

				// TODO: find component class based on @return rule using arachne/class-resolver and check it's class-level rules
				// component name should be $component->getName() . '-' . $name this time
			}

		} catch (BadRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

}
