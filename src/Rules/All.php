<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Rules;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
class All extends ValidationRule
{

	/** @var \Arachne\Verifier\RuleInterface[] */
	public $rules = [];

}
