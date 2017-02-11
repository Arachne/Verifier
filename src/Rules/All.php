<?php

namespace Arachne\Verifier\Rules;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
class All extends ValidationRule
{
    /**
     * @var \Arachne\Verifier\RuleInterface[]
     */
    public $rules = [];
}
