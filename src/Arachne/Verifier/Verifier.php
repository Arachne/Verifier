<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Arachne\Verifier\Exception\ForbiddenRequestException;
use Arachne\Verifier\Exception\InvalidArgumentException;
use Doctrine\Common\Annotations\Reader;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
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
	 * @throws ForbiddenRequestException
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
			$this->handlerLoader
				->getAnnotationHandler(get_class($annotation))
				->checkAnnotation($annotation, $request);
		}
	}

	/**
	 * Checks whether it is possible to run the given request.
	 * @param Request $request
	 * @return bool
	 */
	public function isLinkVerified(Request $request)
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
