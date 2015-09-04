<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
interface RuleProviderInterface
{

	/**
	 * @param ReflectionClass|ReflectionMethod|ReflectionProperty $reflection
	 * @return RuleInterface[]
	 */
	public function getRules(Reflector $reflection);

}
