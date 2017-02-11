<?php

namespace Arachne\Verifier;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
interface RuleInterface
{
    /**
     * Specifies HTTP code to use when the rule fails.
     *
     * @return int
     */
    public function getCode();
}
