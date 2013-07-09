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
trait TPresenter
{

	/** @var \Arachne\Verifier\Verifier */
	protected $verifier;

	/** @var \Doctrine\Common\Annotations\Reader */
	protected $annotationsReader;

	/**
	 * @param \Arachne\Verifier\Verifier $verifier
	 * @param \Doctrine\Common\Annotations\Reader $reader
	 */
	public function injectVerifier(Verifier $verifier, \Doctrine\Common\Annotations\Reader $reader)
	{
		$this->verifier = $verifier;
		$this->annotationsReader = $reader;
	}

	/**
	 * @param \Nette\Reflection\ClassType|\Nette\Reflection\Method $element
	 */
	public function checkRequirements($reflection)
	{
		if ($reflection instanceof \ReflectionMethod)  {
			$annotations = $this->annotationsReader->getMethodAnnotations($reflection);
		} elseif ($reflection instanceof \ReflectionClass) {
			$annotations = $this->annotationsReader->getClassAnnotations($reflection);
		} else {
			throw new InvalidArgumentException('Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.');
		}
		$this->verifier->checkAnnotations($annotations, $this->getParameters(), $this->getName());
	}

	/**
	 * @param string $destination
	 * @param mixed[] $parameters
	 */
	protected function redirectIfVerified($destination, $parameters = [])
	{
		$link = $this->link($destination, $parameters);
		if ($this->verifier->isLinkAvailable($this->getLastCreatedRequest())) {
			$this->redirectUrl($link);
		}
	}

}
