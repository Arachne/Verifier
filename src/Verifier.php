<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Arachne\DIHelpers\ResolverInterface;
use Arachne\Verifier\Exception\InvalidArgumentException;
use Arachne\Verifier\Exception\UnexpectedTypeException;
use Nette\Application\BadRequestException;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\PresenterComponent;
use Nette\Application\UI\PresenterComponentReflection;
use Nette\Object;
use Nette\Utils\Callback;
use ReflectionClass;
use ReflectionMethod;
use Reflector;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Verifier extends Object
{

	/** @var RuleProviderInterface */
	private $ruleProvider;

	/** @var ResolverInterface */
	private $handlerResolver;

	/** @var IPresenterFactory */
	private $presenterFactory;

	/** @var RuleInterface[][] */
	private $cache;

	public function __construct(RuleProviderInterface $ruleProvider, ResolverInterface $handlerResolver, IPresenterFactory $presenterFactory)
	{
		$this->ruleProvider = $ruleProvider;
		$this->handlerResolver = $handlerResolver;
		$this->presenterFactory = $presenterFactory;
	}

	/**
	 * Returns rules that are required for given reflection.
	 * @param ReflectionClass|ReflectionMethod $reflection
	 * @return RuleInterface[]
	 */
	public function getRules(Reflector $reflection)
	{
		if (!$reflection instanceof ReflectionMethod && !$reflection instanceof ReflectionClass) {
			throw new InvalidArgumentException('Reflection must be an instance of either ReflectionMethod or ReflectionClass.');
		}

		$key = ($reflection instanceof ReflectionMethod ? $reflection->getDeclaringClass()->getName() . '::' : '') . $reflection->getName();
		if (!isset($this->cache[$key])) {
			$this->cache[$key] = $this->ruleProvider->getRules($reflection);
		}

		return $this->cache[$key];
	}

	/**
	 * Checks whether the given rules are met.
	 * @param RuleInterface[] $rules
	 * @param Request $request
	 * @param string $component
	 * @throws BadRequestException
	 */
	public function checkRules(array $rules, Request $request, $component = NULL)
	{
		foreach ($rules as $rule) {
			$class = get_class($rule);
			$handler = $this->handlerResolver->resolve($class);
			if (!$handler instanceof RuleHandlerInterface) {
				throw new UnexpectedTypeException("No rule handler found for type '$class'.");
			}
			$handler->checkRule($rule, $request, $component);
		}
	}

	/**
	 * Checks whether all rules of the given reflection are met.
	 * @param ReflectionClass|ReflectionMethod $reflection
	 * @param Request $request
	 * @param string $component
	 * @throws BadRequestException
	 */
	public function checkReflection(Reflector $reflection, Request $request, $component = NULL)
	{
		$rules = $this->getRules($reflection);
		$this->checkRules($rules, $request, $component);
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
					$name = $component->getUniqueId();
					if ($name !== substr($signalId, 0, $pos)) {
						throw new InvalidArgumentException("Wrong signal receiver, expected '" . substr($signalId, 0, $pos) . "' component but '$name' was given.");
					}
					$reflection = new PresenterComponentReflection($component);
					$signal = substr($signalId, $pos + 1);
				} else {
					// signal for presenter
					$name = NULL;
					$presenter = $request->getPresenterName();
					$reflection = new PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));
					$signal = $signalId;
				}

				// Signal requirements
				$method = 'handle' . $signal;
				if ($reflection->hasCallableMethod($method)) {
					$this->checkReflection($reflection->getMethod($method), $request, $name);
				}

			} else {
				$presenter = $request->getPresenterName();
				$reflection = new PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));

				// Presenter requirements
				$this->checkReflection($reflection, $request);

				// Action requirements
				$action = $parameters[Presenter::ACTION_KEY];
				$method = 'action' . $action;
				if ($reflection->hasCallableMethod($method)) {
					$this->checkReflection($reflection->getMethod($method), $request);
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
				$this->checkReflection($factory, $request, $parent->getParent() ? $parent->getUniqueId() : NULL);

				// TODO: find component class based on @return rule and check it's class-level rules
				// component name should be $component->getName() . '-' . $name this time
			}

		} catch (BadRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

}
