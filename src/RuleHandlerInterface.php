<?php

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
     * @param Request       $request
     * @param string        $component
     *
     * @throws VerificationException
     */
    public function checkRule(RuleInterface $rule, Request $request, $component = null);
}
