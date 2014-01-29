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
use Doctrine\Common\Annotations\Reader;
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

	/** @var Reader */
	protected $reader;

	/** @var IAnnotationHandlerLoader */
	protected $handlerLoader;

	/** @var IPresenterFactory */
	protected $presenterFactory;

	public function __construct(Reader $reader, IAnnotationHandlerLoader $handlerLoader, IPresenterFactory $presenterFactory)
	{
		$this->reader = $reader;
		$this->handlerLoader = $handlerLoader;
		$this->presenterFactory = $presenterFactory;
	}

	/**
	 * Checks whether the given reflection contains any conditions that are not met.
	 * @param ReflectionClass|ReflectionMethod $annotations
	 * @param Request $request
	 * @param string $component
	 * @throws BadRequestException
	 */
	public function checkAnnotations(Reflector $reflection, Request $request, $component = NULL)
	{
		if ($reflection instanceof ReflectionMethod) {
			$annotations = $this->reader->getMethodAnnotations($reflection);
		} elseif ($reflection instanceof ReflectionClass) {
			$annotations = $this->reader->getClassAnnotations($reflection);
		} else {
			throw new InvalidArgumentException('Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.');
		}

		foreach ($annotations as $annotation) {
			if (!$annotation instanceof IAnnotation) {
				continue;
			}
			$this->handlerLoader
				->getAnnotationHandler(get_class($annotation))
				->checkAnnotation($annotation, $request, $component);
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
					$this->checkAnnotations($reflection->getMethod($method), $request, $component->getParent() === $component ? NULL : $component->getName());
				}

			} else {
				$presenter = $request->getPresenterName();
				$reflection = new PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));

				// Presenter requirements
				$this->checkAnnotations($reflection, $request);

				// Action requirements
				$action = $parameters[Presenter::ACTION_KEY];
				$method = 'action' . $action;
				if ($reflection->hasCallableMethod($method)) {
					$this->checkAnnotations($reflection->getMethod($method), $request);
				}
				$method = 'render' . $action;
				if ($reflection->hasCallableMethod($method)) {
					$this->checkAnnotations($reflection->getMethod($method), $request);
				}
			}
		} catch (BadRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Checks whether the parent component can create the subcomponent with given name.
	 * @param Request $request
	 * @param PresenterComponent $parent
	 * @param string $name
	 * @return bool
	 */
	public function isComponentVerified(Request $request, PresenterComponent $parent, $name)
	{
		$reflection = new PresenterComponentReflection($parent);

		try {
			$method = 'createComponent' . ucfirst($name);
			if ($reflection->hasMethod($method)) {
				$factory = $reflection->getMethod($method);
				$this->checkAnnotations($factory, $request, $parent->getParent() === $parent ? NULL : $parent->getName());

				// TODO: find component class based on @return annotation using arachne/class-resolver and check it's class-level annotations
				// component name should be $component->getName() . '-' . $name this time
			}

		} catch (BadRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

}
