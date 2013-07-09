<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Security;

class SecurityAnnotationHandler extends \Nette\Object implements \Arachne\Verifier\IAnnotationHandler
{

	/** @var \Nette\Security\User */
	protected $user;

	/**
	 * @param \Nette\Security\User $user
	 */
	public function __construct(\Nette\Security\User $user)
	{
		$this->user = $user;
	}

	/**
	 * @param \Arachne\Verifier\IRule $rule
	 * @param string $presenter
	 * @param array $parameters
	 * @throws \Arachne\Verifier\Security\FailedAuthorizationException
	 * @throws \Arachne\Verifier\Security\FailedAuthenticationException
	 */
	public function checkAnnotation(\Arachne\Verifier\IRule $rule, $presenter, array $parameters)
	{
		if ($rule instanceof Allowed) {
			$resource = $rule->resource;
			if (\Nette\Utils\Strings::startsWith($resource, '$')) {
				$parameter = substr($resource, 1);
				if ($parameter === 'this') {
					$resource = substr($presenter, strrpos(':' . $presenter, ':'));
				} elseif (!isset($parameters[$parameter])) {
					throw new \Arachne\Verifier\InvalidStateException("Missing parameter '$resource'.");
				} elseif (!$parameters[$parameter] instanceof \Nette\Security\IResource) {
					throw new \Arachne\Verifier\InvalidStateException("Parameter '$resource' is not instance of \Nette\Security\IResource.");
				} else {
					$resource = $parameters[$parameter];
				}
			}
			if (!$this->user->isAllowed($resource, $rule->privilege)) {
				if ($resource instanceof \Nette\Security\IResource) {
					$resource = $resource->getResourceId();
				}
				throw new FailedAuthorizationException("Required privilege '{$resource} / {$rule->privilege}' is not granted.");
			}
		} elseif ($rule instanceof InRole) {
			if (!$this->user->isInRole($rule->role)) {
				throw new FailedAuthorizationException("Role '{$rule->role}' is required for this request.");
			}
		} elseif ($rule instanceof LoggedIn) {
			if ($this->user->isLoggedIn() != $rule->flag) {
				throw new FailedAuthenticationException('User must ' . ($rule->flag ? '' : 'not ') . 'be logged in for this request.');
			}
		} else {
			throw new \Arachne\Verifier\InvalidArgumentException('Unknown annotation \'' . get_class($rule) . '\' given.');
		}
	}

}
