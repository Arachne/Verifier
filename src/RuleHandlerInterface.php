<?php

/*
 * This file is part of the Arachne package.
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Arachne\Verifier\Exception\VerificationException;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
interface RuleHandlerInterface
{

	/**
	 * @param RuleInterface $rule
	 * @param Request $request
	 * @param string $component
	 * @throws VerificationException
	 */
	public function checkRule(RuleInterface $rule, Request $request, $component = null);

}
