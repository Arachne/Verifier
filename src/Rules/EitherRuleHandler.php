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
class EitherRuleHandler implements RuleHandlerInterface
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
     * @param Either $rule
     *
     * @throws VerificationException
     */
    public function checkRule(RuleInterface $rule, Request $request, ?string $component = null): void
    {
        if (!$rule instanceof Either) {
            throw new InvalidArgumentException('Unknown rule \''.get_class($rule).'\' given.');
        }

        foreach ($rule->rules as $value) {
            try {
                $this->verifier->checkRules([$value], $request, $component);

                return;
            } catch (VerificationException $e) {
                // intentionally ignored
            }
        }

        throw new VerificationException($rule, 'None of the rules was met.');
    }
}
