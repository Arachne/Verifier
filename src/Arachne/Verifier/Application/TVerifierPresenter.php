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
	 * @param ClassType|Method $element
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
		if ($this->verifier->isLinkAvailable($this->getLastCreatedRequest())) {
			$this->redirectUrl($link);
		}
	}

}
