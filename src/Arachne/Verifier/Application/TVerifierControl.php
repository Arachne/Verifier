<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Application;

use Nette\Reflection\ClassType;
use Nette\Reflection\Method;

/**
 * @author Jáchym Toušek
 * @todo injectVerifier method and check if it was injected in attached method
 */
trait TVerifierControl
{

	/**
	 * @param ClassType|Method $element
	 */
	public function checkRequirements($reflection)
	{
		$this->getPresenter()->getVerifier()->checkAnnotations($reflection, $this->getPresenter()->getRequest(), $this->getName());
	}

	/**
	 * @todo Remove this method or add canCreateComponent and verifiedLink methods and move all three to a separate trait TVerifierHelpers
	 * @param string $destination
	 * @param mixed[] $parameters
	 */
	protected function redirectVerified($destination, $parameters = [])
	{
		$link = $this->link($destination, $parameters);
		if ($this->getPresenter()->getVerifier()->isLinkVerified($this->getLastCreatedRequest(), $this)) {
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
