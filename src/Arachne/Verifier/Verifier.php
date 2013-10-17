<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Doctrine\Common\Annotations\Reader;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\PresenterComponentReflection;
use Nette\DI\Container;
use Nette\Object;
use ReflectionClass;
use ReflectionMethod;
use Reflector;

/**
 * @author Jáchym Toušek
 */
class Verifier extends Object
{

	/** @var IPresenterFactory */
	protected $presenterFactory;

	/** @var Reader */
	protected $reader;

	/** @var Container */
	protected $container;

	/** @var IAnnotationHandler[] */
	private $handlers;

	/**
	 * @param Reader $reader
	 * @param Container $container $container
	 * @param IPresenterFactory $presenterFactory
	 */
	public function __construct(Reader $reader, Container $container, IPresenterFactory $presenterFactory)
	{
		$this->reader = $reader;
		$this->container = $container;
		$this->presenterFactory = $presenterFactory;
		$this->handlers = array();
	}

	/**
	 * @param ReflectionClass|ReflectionMethod $annotations
	 * @param Request $request
	 * @throws \Arachne\Verifier\ForbiddenRequestException
	 */
	public function checkAnnotations(Reflector $reflection, Request $request)
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
			$class = $annotation->getHandlerClass();
			if (!isset($this->handlers[$class])) {
				$this->handlers[$class] = $this->container->getByType($class);
				if (!$this->handlers[$class] instanceof IAnnotationHandler) {
					throw new InvalidStateException('Class \'' . get_class($this->handlers[$class]) . '\' does not implement \Arachne\Verifier\IAnnotationHandler interface.');
				}
			}
			$this->handlers[$class]->checkAnnotation($annotation, $request);
		}
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function isLinkAvailable(Request $request)
	{
		$presenter = $request->getPresenterName();
		$parameters = $request->getParameters();
		$presenterReflection = new PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));

		try {
			// Presenter requirements
			$this->checkAnnotations($presenterReflection, $request);

			// Action requirements
			$action = $parameters[Presenter::ACTION_KEY];
			$method = 'action' . $action;
			$actionReflection = $presenterReflection->hasCallableMethod($method) ? $presenterReflection->getMethod($method) : NULL;
			if ($actionReflection) {
				$this->checkAnnotations($actionReflection, $request);
			}
			$method = 'render' . $action;
			$viewReflection = $presenterReflection->hasCallableMethod($method) ? $presenterReflection->getMethod($method) : NULL;
			if ($viewReflection) {
				$this->checkAnnotations($viewReflection, $request);
			}

			// Signal requirements
			if (isset($parameters[Presenter::SIGNAL_KEY])) {
				$signal = $parameters[Presenter::SIGNAL_KEY];
				$method = 'handle' . $signal;
				if ($presenterReflection->hasCallableMethod($method)) {
					$reflection = $presenterReflection->getMethod($method);
					$this->checkAnnotations($reflection, $request);
				}
			}

		} catch (ForbiddenRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

}
