<?php

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
     * @param RuleInterface $rule
     * @param Request       $request
     * @param string        $component
     *
     * @throws VerificationException
     */
    public function checkRule(RuleInterface $rule, Request $request, $component = null)
    {
        if (!$rule instanceof Enabled) {
            throw new \InvalidArgumentException(sprintf('Unknown rule "%s" given.', get_class($rule)));
        }

        if (is_string($rule->value)) {
            $parameters = $request->getParameters();
            $parameter = ($component ? $component.'-' : '').ltrim($rule->value, '$');
            $enabled = isset($parameters[$parameter]) && (bool) $parameters[$parameter];
        } else {
            $enabled = $rule->value;
        }

        if (!$enabled) {
            throw new VerificationException($rule, 'This action is not enabled.');
        }
    }
}
