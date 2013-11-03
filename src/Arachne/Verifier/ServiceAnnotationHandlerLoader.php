<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Arachne\Verifier\Exception\UnexpectedTypeException;
use Nette\DI\Container;
use Nette\Object;

/**
 * @author Jáchym Toušek
 */
class ServiceAnnotationHandlerLoader extends Object implements IAnnotationHandlerLoader
{

	/** @var Container */
	private $container;

	/** @var string[] */
	private $services;

	/** @var IAnnotationHandler[] */
	private $handlers;

	public function __construct($services, Container $container)
	{
		$this->services = $services;
		$this->container = $container;
	}

	/**
	 * @param string $type
	 * @return IAnnotationHandler
	 */
	public function getAnnotationHandler($type)
	{
		if (!isset($this->services[$type])) {
			throw new UnexpectedTypeException("No annotation handler found for type '$type'.");
		}
		$name = $this->services[$type];
		if (!isset($this->handlers[$name])) {
			$service = $this->container->getService($name);
			if (!$service instanceof IAnnotationHandler) {
				throw new UnexpectedTypeException("Service '$name' is not an instance of Arachne\Verifier\IAnnotationHandler.");
			}
			$this->handlers[$name] = $service;
		}
		return $this->handlers[$name];
	}

}
