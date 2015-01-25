<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Arachne\DIHelpers\ResolverInterface;
use Reflector;

/**
 * @author Jáchym Toušek
 */
class ChainRuleProvider implements RuleProviderInterface
{

	/** @var ResolverInterface */
	private $providerResolver;

	/**
	 * @param ResolverInterface $providerResolver
	 */
	public function __construct(ResolverInterface $providerResolver)
	{
		$this->providerResolver = $providerResolver;
	}

	/**
	 * @param ReflectionClass|ReflectionMethod $reflection
	 * @return RuleInterface[]
	 */
	public function getRules(Reflector $reflection)
	{
		$rules = array();
		foreach ($this->providerResolver as $provider) {
			$rules += $provider->getRules($reflection);
		}
		return $rules;
	}

}
