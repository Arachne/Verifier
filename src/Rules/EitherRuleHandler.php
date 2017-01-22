<?php

/*
 * This file is part of the Arachne package.
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\Exception\InvalidArgumentException;
use Arachne\Verifier\Exception\VerificationException;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\RuleHandlerInterface;
use Arachne\Verifier\Verifier;
use Nette\Application\Request;
use Nette\Object;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class EitherRuleHandler extends Object implements RuleHandlerInterface
{

	/** @var Verifier */
	private $verifier;

	/**
	 * @param Verifier $verifier
	 */
	public function __construct(Verifier $verifier)
	{
		$this->verifier = $verifier;
	}

	/**
	 * @param Either $rule
	 * @param Request $request
	 * @param string $component
	 * @throws VerificationException
	 */
	public function checkRule(RuleInterface $rule, Request $request, $component = null)
	{
		if (!$rule instanceof Either) {
			throw new InvalidArgumentException('Unknown rule \'' . get_class($rule) . '\' given.');
		}

		foreach ($rule->rules as $value) {
			try {
				$this->verifier->checkRules([ $value ], $request, $component);
				return;
			} catch (VerificationException $e) {
				// intentionally ignored
			}
		}
		throw new VerificationException($rule, 'None of the rules was met.');
	}

}
