<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Application;

use Arachne\Verifier\Verifier;
use Nette\ComponentModel\IComponent;
use Nette\Reflection\ClassType;
use Nette\Reflection\Method;

/**
 * @author Jáchym Toušek
 */
trait TVerifierPresenter
{

	/** @var Verifier */
	protected $verifier;

	/**
	 * @param Verifier $verifier
	 */
	final public function injectVerifier(Verifier $verifier)
	{
		$this->verifier = $verifier;
	}

	/**
	 * @param ClassType|Method $reflection
	 */
	public function checkRequirements($reflection)
	{
		$this->verifier->checkAnnotations($reflection, $this->getRequest());
	}

	/**
	 * @param string $destination
	 * @param mixed[] $parameters
	 */
	protected function redirectVerified($destination, $parameters = [])
	{
		$link = $this->link($destination, $parameters);
		if ($this->verifier->isLinkVerified($this->getLastCreatedRequest())) {
			$this->redirectUrl($link);
		}
	}

	/**
	 * Component factory. Delegates the creation of components to a createComponent<Name> method.
	 * @param string $name
	 * @return IComponent|null
	 */
	protected function createComponent($name)
	{
		$method = 'createComponent' . ucfirst($name);
		if (method_exists($this, $method)) {
			$this->checkRequirements($this->getReflection()->getMethod($method));
		}

		return parent::createComponent($name);
	}

}
