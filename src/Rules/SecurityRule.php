<?php

/*
 * This file is part of the Arachne package.
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\RuleInterface;
use Nette\Object;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
abstract class SecurityRule extends Object implements RuleInterface
{

	/**
	 * @return int
	 */
	public function getCode()
	{
		return 403;
	}

}
