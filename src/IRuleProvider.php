<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Reflector;

/**
 * @author Jáchym Toušek
 */
interface IRuleProvider
{

	/**
	 * @param ReflectionClass|ReflectionMethod $reflection
	 * @return IRule[]
	 */
	public function getRules(Reflector $reflection);

}
