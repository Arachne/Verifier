<?php

/**
 * This file is part of the Arachne
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

	use TVerifierControl;

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
		$this->verifier->checkRules($reflection, $this->getRequest());
	}

	/**
	 * @return Verifier
	 */
	public function getVerifier()
	{
		return $this->verifier;
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
