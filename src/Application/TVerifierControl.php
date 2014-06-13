<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Application;

use ReflectionClass;
use ReflectionMethod;

/**
 * @author Jáchym Toušek
 * @todo injectVerifier method and check if it was injected in attached method
 */
trait TVerifierControl
{

	/**
	 * @param ReflectionClass|ReflectionMethod $element
	 */
	public function checkRequirements($reflection)
	{
		$this->getPresenter()->getVerifier()->checkRules($reflection, $this->getPresenter()->getRequest(), $this->getName());
	}

	/**
	 * @todo Remove this method or add canCreateComponent and verifiedLink methods and move all three to a separate trait TVerifierHelpers
	 * @param int $code
	 * @param string $destination
	 * @param mixed[] $parameters
	 */
	protected function redirectVerified($code, $destination = NULL, $args = [])
	{
		if (!is_numeric($code)) { // first parameter is optional
			$args = $destination;
			$destination = $code;
			$code = NULL;
		}

		if (!is_array($args)) {
			$args = array_slice(func_get_args(), is_numeric($code) ? 2 : 1);
		}

		$link = $this->link($destination, $args);
		if ($this->getPresenter()->getVerifier()->isLinkVerified($this->getLastCreatedRequest(), $this)) {
			$this->redirectUrl($link, $code);
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
