<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Application;

/**
 * @author Jáchym Toušek
 */
trait TVerificationPresenter
{

	/** @var \Arachne\Verifier\Verifier */
	protected $verifier;

	/**
	 * @param \Arachne\Verifier\Verifier $verifier
	 */
	final public function injectVerifier(\Arachne\Verifier\Verifier $verifier)
	{
		$this->verifier = $verifier;
	}

	/**
	 * @param \Nette\Reflection\ClassType|\Nette\Reflection\Method $element
	 */
	public function checkRequirements($reflection)
	{
		$this->verifier->checkAnnotations($reflection);
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
