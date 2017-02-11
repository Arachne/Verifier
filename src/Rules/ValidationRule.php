<?php

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\RuleInterface;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
abstract class ValidationRule implements RuleInterface
{
    /**
     * @return int
     */
    public function getCode()
    {
        return 404;
    }
}
