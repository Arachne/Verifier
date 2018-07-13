<?php

declare(strict_types=1);

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\RuleInterface;
use PHPStan\Rules\Rule;

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
        /** @var RuleInterface|null $rule */
        $rule = reset($this->rules);

        return $rule instanceof RuleInterface ? $rule->getCode() : 404;
    }
}
