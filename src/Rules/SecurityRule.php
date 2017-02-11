<?php

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\RuleInterface;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
abstract class SecurityRule implements RuleInterface
{
    /**
     * @return int
     */
    public function getCode()
    {
        return 403;
    }
}
