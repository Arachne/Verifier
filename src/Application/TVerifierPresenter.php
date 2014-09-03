<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Application;

use Arachne\Verifier\Exception\NotSupportedException;
use Arachne\Verifier\Verifier;
use Nette\Application\BadRequestException;
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
		$rules = $this->verifier->getRules($reflection);
		if (!empty($rules) && $reflection instanceof ReflectionMethod && substr($reflection->getName(), 0, 6) === 'render') {
			throw new NotSupportedException('Rules for render methods are not supported. Define the rules for action method instead.');
		}
		$this->verifier->checkRules($rules, $this->getRequest());
	}

	/**
	 * @return Verifier
	 */
	public function getVerifier()
	{
		return $this->verifier;
	}

	/**
	 * Ensures that the action method exists.
	 * @param string
	 * @param array
	 * @return bool
	 */
	protected function tryCall($method, array $params)
	{
		$called = parent::tryCall($method, $params);
		if (!$called && substr($method, 0, 6) === 'action') {
			$class = get_class($this);
			throw new BadRequestException("Action '$class::$method' does not exist.");
		}
		return $called;
	}

}
