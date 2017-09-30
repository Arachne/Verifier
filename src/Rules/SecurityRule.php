<?php

declare(strict_types=1);

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\RuleInterface;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
abstract class SecurityRule implements RuleInterface
{
    public function getCode(): int
    {
        return 403;
    }
}
