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

    public function getCode(): int
    {
        return $this->rules ? reset($this->rules)->getCode() : 404;
    }
}
