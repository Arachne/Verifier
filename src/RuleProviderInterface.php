<?php

declare(strict_types=1);

namespace Arachne\Verifier;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
interface RuleProviderInterface
{
    /**
     * @return RuleInterface[]
     */
    public function getRules(Reflector $reflection): array;
}
