<?php

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
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty $reflection
     *
     * @return RuleInterface[]
     */
    public function getRules(Reflector $reflection);
}
