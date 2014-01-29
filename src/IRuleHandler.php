<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Nette\Application\BadRequestException;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek
 */
interface IRuleHandler
{

	/**
	 * @param IRule $rule
	 * @param Request $request
	 * @param string $component
	 * @throws BadRequestException
	 */
	public function checkRule(IRule $rule, Request $request, $component = NULL);

}
