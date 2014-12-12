<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\Exception\FailedEitherVerificationException;
use Arachne\Verifier\Exception\InvalidArgumentException;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\RuleHandlerInterface;
use Arachne\Verifier\Verifier;
use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Nette\Object;

/**
 * @author Jáchym Toušek
 */
class CascadeRuleHandler extends Object implements RuleHandlerInterface
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
	 * @param RuleInterface $rule
	 * @param Request $request
	 * @param string $component
	 * @throws BadRequestException
	 */
	public function checkRule(RuleInterface $rule, Request $request, $component = NULL)
	{
		if ($rule instanceof Either) {
			$this->checkRuleEither($rule, $request, $component);
		} elseif ($rule instanceof All) {
			$this->checkRuleAll($rule, $request, $component);
		} else {
			throw new InvalidArgumentException('Unknown rule \'' . get_class($rule) . '\' given.');
		}
	}

	/**
	 * @param Either $rule
	 * @param Request $request
	 * @param string $component
	 * @throws FailedEitherVerificationException
	 */
	private function checkRuleEither(Either $rule, Request $request, $component)
	{
		foreach ($rule->rules as $value) {
			try {
				$this->verifier->checkRules(array($value), $request, $component);
				return;
			} catch (BadRequestException $e) {
				// intentionally ignored
			}
		}
		throw new FailedEitherVerificationException('None of the rules was met.');
	}

	/**
	 * @param All $rule
	 * @param Request $request
	 * @param string $component
	 * @throws BadRequestException
	 */
	private function checkRuleAll(All $rule, Request $request, $component)
	{
		$this->verifier->checkRules($rule->rules, $request, $component);
	}

}
