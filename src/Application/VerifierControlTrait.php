<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Application;

use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 * @todo injectVerifier method and check if it was injected in attached method
 */
trait VerifierControlTrait
{

	/** @var callable[] */
	public $onPresenter;

	/**
	 * @param ReflectionClass|ReflectionMethod $reflection
	 */
	public function checkRequirements($reflection)
	{
		$this->getPresenter()->getVerifier()->checkReflection($reflection, $this->getPresenter()->getRequest(), $this->getUniqueId());
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
			$parameters = is_array($destination) ? $destination : array_slice(func_get_args(), 1);
			$destination = $code;
			$code = null;

		} elseif (!is_array($parameters)) {
			$parameters = array_slice(func_get_args(), 2);
		}

		$presenter = $this->getPresenter();
		$link = $presenter->createRequest($this, $destination, $parameters, 'redirect');
		if ($presenter->getVerifier()->isLinkVerified($presenter->getLastCreatedRequest(), $this)) {
			$presenter->redirectUrl($link, $code);
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

	/**
	 * This method will be called when the component (or component's parent) becomes attached to a monitored object. Do not call this method yourself.
	 * @param IComponent
	 */
	protected function attached($component)
	{
		if ($component instanceof Presenter) {
			$this->onPresenter($component);
		}
		parent::attached($component);
	}

}
