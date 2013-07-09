<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

/**
 * @author Jáchym Toušek
 */
class Verifier extends \Nette\Object
{

	/** @var \Nette\Application\IPresenterFactory */
	protected $presenterFactory;

	/** @var \Doctrine\Common\Annotations\Reader */
	protected $reader;

	/** @var \Nette\DI\Container */
	protected $container;

	/**
	 * @param \Nette\Application\IPresenterFactory $presenterFactory
	 * @param \Doctrine\Common\Annotations\Reader $reader
	 * @param \Nette\DI\Container $container $container
	 */
	public function __construct(\Nette\Application\IPresenterFactory $presenterFactory, \Doctrine\Common\Annotations\Reader $reader, \Nette\DI\Container $container)
	{
		$this->presenterFactory = $presenterFactory;
		$this->reader = $reader;
		$this->container = $container;
	}

	/**
	 * @param \ReflectionClass|\ReflectionMethod $annotations
	 */
	public function checkAnnotations(\Reflector $reflection)
	{
		if ($reflection instanceof \ReflectionMethod)  {
			$requirements = $this->reader->getMethodAnnotation($reflection, 'Arachne\Verifier\Requirements');
		} elseif ($reflection instanceof \ReflectionClass) {
			$requirements = $this->reader->getClassAnnotation($reflection, 'Arachne\Verifier\Requirements');
		} else {
			throw new InvalidArgumentException('Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.');
		}

		static $handlers = [];
		if ($requirements !== NULL) {
			foreach ($requirements->rules as $rule) {
				$class = $rule->getHandlerClass();
				if (!isset($handlers[$class])) {
					$handlers[$class] = $this->container->getByType($class);
					if (!$handlers[$class] instanceof IAnnotationHandler) {
						throw new InvalidStateException('Class \'' . get_class($handlers[$class]) . '\' does not implement \Arachne\Verifier\IAnnotationHandler interface.');
					}
				}
				$handlers[$class]->checkAnnotation($rule);
			}
		}
	}

	/**
	 * @param \Nette\Application\Request $request
	 * @return bool
	 */
	public function isLinkAvailable(\Nette\Application\Request $request)
	{
		$presenter = $request->getPresenterName();
		$parameters = $request->getParameters();
		$presenterReflection = new \Nette\Application\UI\PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));

		try {
			// Presenter requirements
			$this->checkAnnotations($presenterReflection);

			// Action requirements
			$action = $parameters[\Nette\Application\UI\Presenter::ACTION_KEY];
			$method = 'action' . $action;
			$reflection = $presenterReflection->hasCallableMethod($method) ? $presenterReflection->getMethod($method) : NULL;
			if (!$reflection) {
				$method = 'render' . $action;
				$reflection = $presenterReflection->hasCallableMethod($method) ? $presenterReflection->getMethod($method) : NULL;
			}
			if ($reflection) {
				$this->checkAnnotations($reflection);
			}

			// Signal requirements
			if (isset($parameters[\Nette\Application\UI\Presenter::SIGNAL_KEY])) {
				$signal = $parameters[\Nette\Application\UI\Presenter::SIGNAL_KEY];
				$method = 'handle' . ucfirst($signal);
				if ($presenterReflection->hasCallableMethod($method)) {
					$reflection = $presenterReflection->getMethod($method);
					$this->checkAnnotations($reflection);
				}
			}

		} catch (\Nette\Application\ForbiddenRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

}
