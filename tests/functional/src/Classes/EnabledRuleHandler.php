<?php

declare(strict_types=1);

namespace Tests\Functional\Classes;

use Arachne\Verifier\Exception\VerificationException;
use Arachne\Verifier\RuleHandlerInterface;
use Arachne\Verifier\RuleInterface;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class EnabledRuleHandler implements RuleHandlerInterface
{
    /**
     * @throws VerificationException
     */
    public function checkRule(RuleInterface $rule, Request $request, ?string $component = null): void
    {
        if (!$rule instanceof Enabled) {
            throw new \InvalidArgumentException(sprintf('Unknown rule "%s" given.', get_class($rule)));
        }

        if (is_string($rule->value)) {
            $parameters = $request->getParameters();
            $parameter = ($component !== null ? $component.'-' : '').ltrim($rule->value, '$');
            $enabled = isset($parameters[$parameter]) && (bool) $parameters[$parameter];
        } else {
            $enabled = (bool) $rule->value;
        }

        if (!$enabled) {
            throw new VerificationException($rule, 'This action is not enabled.');
        }
    }
}
