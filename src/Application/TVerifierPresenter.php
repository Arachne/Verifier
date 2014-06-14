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
use ReflectionClass;
use ReflectionMethod;

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
	 * @param ReflectionClass|ReflectionMethod $element
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

}
