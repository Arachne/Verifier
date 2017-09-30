<?php

declare(strict_types=1);

namespace Arachne\Verifier;

use Arachne\Verifier\Exception\VerificationException;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
interface RuleHandlerInterface
{
    /**
     * @throws VerificationException
     */
    public function checkRule(RuleInterface $rule, Request $request, ?string $component = null): void;
}
