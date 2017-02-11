<?php

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\RuleInterface;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
class Either implements RuleInterface
{
    /**
     * @var \Arachne\Verifier\RuleInterface[]
     */
    public $rules = [];

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->rules ? reset($this->rules)->getCode() : 404;
    }
}
