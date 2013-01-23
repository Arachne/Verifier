<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

/**
 * @author Jáchym Toušek
 */
class Verifier extends \Nette\Object
{

	/** @var \Nette\Application\IPresenterFactory */
	protected $presenterFactory;

	/** @var \Nette\Security\User */
	protected $user;

	/**
	 * @param \Nette\Application\IPresenterFactory $presenterFactory
	 * @param \Nette\Security\User $verifier
	 */
	public function __construct(\Nette\Application\IPresenterFactory $presenterFactory, \Nette\Security\User $user)
	{
		$this->presenterFactory = $presenterFactory;
		$this->user = $user;
	}

	/**
	 * @param mixed[] $annotations
	 * @throws FailedAuthenticationException
	 * @throws FailedAuthorizationException
	 * @throws InvalidStateException
	 */
	public function checkAnnotations(array $annotations)
	{
		// @LoggedIn
		if (isset($annotations['LoggedIn']) && !$this->user->isLoggedIn()) {
			throw new FailedAuthenticationException("User isn't logged in.");
		}

		// @InRole <role>
		$roles = isset($annotations['InRole']) ? $annotations['InRole'] : [];
		foreach ($roles as $role) {
			if (!$this->user->isInRole($role)) {
				throw new FailedAuthorizationException("Required role '$role'.");
			}
		}

		// @Allowed (<resource>, <privilege>)
		$privileges = isset($annotations['Allowed']) ? $annotations['Allowed'] : [];
		foreach ($privileges as $item) {
			if (is_string($item)) {
				$item = [ $item ];
			}
			$resource = isset($item[0]) ? $item[0] : \Nette\Security\IAuthorizator::ALL;
			$privilege = isset($item[1]) ? $item[1] : \Nette\Security\IAuthorizator::ALL;

			if (!$this->user->isAllowed($resource, $privilege)) {
				if ($resource instanceof \Nette\Security\IResource) {
					$resource = $resource->getResourceId();
				}
				throw new FailedAuthorizationException("Required privilege '$resource / $privilege' is not allowed.");
			}
		}
	}

	/**
	 * @param \Nette\Application\Request $request
	 * @return bool
	 */
	public function isLinkAvailable(\Nette\Application\Request $request)
	{
		$presenter = $request->getPresenterName();
		$parameters = $request->getParameters();
		$presenterReflection = new \Nette\Application\UI\PresenterComponentReflection($this->presenterFactory->getPresenterClass($presenter));

		try {
			// Presenter requirements
			$this->checkAnnotations($presenterReflection->getAnnotations());

			// Action requirements
			$action = $parameters[\Nette\Application\UI\Presenter::ACTION_KEY];
			$method = 'action' . $action;
			$element = $presenterReflection->hasCallableMethod($method) ? $presenterReflection->getMethod($method) : NULL;
			if (!$element) {
				$method = 'render' . $action;
				$element = $presenterReflection->hasCallableMethod($method) ? $presenterReflection->getMethod($method) : NULL;
			}
			if ($element) {
				$this->checkAnnotations($element->getAnnotations());
			}

			// Signal requirements
			if (isset($parameters[\Nette\Application\UI\Presenter::SIGNAL_KEY])) {
				$signal = $parameters[\Nette\Application\UI\Presenter::SIGNAL_KEY];
				$method = 'handle' . ucfirst($signal);
				if ($presenterReflection->hasCallableMethod($method)) {
					$element = $presenterReflection->getMethod($method);
					$this->checkAnnotations($element->getAnnotations());
				}
			}

		} catch (\Nette\Application\ForbiddenRequestException $e) {
			return FALSE;
		}

		return TRUE;
	}

}
