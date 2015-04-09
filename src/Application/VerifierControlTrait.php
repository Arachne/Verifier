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
 * @author Jáchym Toušek <enumag@gmail.com>
 * @todo injectVerifier method and check if it was injected in attached method
 */
trait VerifierControlTrait
{

	/**
	 * @param ReflectionClass|ReflectionMethod $reflection
	 */
	public function checkRequirements($reflection)
	{
		$this->getPresenter()->getVerifier()->checkReflection($reflection, $this->getPresenter()->getRequest(), $this->getName());
	}

	/**
	 * @todo Remove this method or add canCreateComponent and verifiedLink methods and move all three to a separate trait TVerifierHelpers
	 * @param int $code
	 * @param string $destination
	 * @param mixed[] $parameters
	 */
	public function redirectVerified($code, $destination = null, $parameters = [])
	{
		// first parameter is optional
		if (!is_numeric($code)) {
			$parameters = $destination;
			$destination = $code;
			$code = null;
		}

		if (!is_array($parameters)) {
			$parameters = array_slice(func_get_args(), is_numeric($code) ? 2 : 1);
		}

		$link = $this->link($destination, $parameters);
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
