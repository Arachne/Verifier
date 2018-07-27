<?php

declare(strict_types=1);

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\Exception\InvalidArgumentException;
use Arachne\Verifier\Exception\VerificationException;
use Arachne\Verifier\RuleHandlerInterface;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\Verifier;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class AllRuleHandler implements RuleHandlerInterface
{
    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * @param Verifier $verifier
     */
    public function __construct(Verifier $verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * @throws VerificationException
     */
    public function checkRule(RuleInterface $rule, Request $request, ?string $component = null): void
    {
        if (!$rule instanceof All) {
            throw new InvalidArgumentException('Unknown rule \''.get_class($rule).'\' given.');
        }

        $this->verifier->checkRules($rule->rules, $request, $component);
    }
}
