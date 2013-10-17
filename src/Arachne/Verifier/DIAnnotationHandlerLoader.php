<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Nette\DI\Container;
use Nette\Object;

/**
 * @author Jáchym Toušek
 */
class DIAnnotationHandlerLoader extends Object implements IAnnotationHandlerLoader
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
	 * @return IAnnotationHandler|NULL
	 */
	public function getAnnotationHandler($type)
	{
		if (!isset($this->services[$type])) {
			return NULL;
		}
		$name = $this->services[$type];
		if (!isset($this->handlers[$name])) {
			$service = $this->container->getService($name);
			if (!$service instanceof IAnnotationHandler) {
				throw new InvalidStateException("Service '$name' is not an instance of Arachne\Verifier\IAnnotationHandler.");
			}
			$this->handlers[$name] = $service;
		}
		return $this->handlers[$name];
	}

}
