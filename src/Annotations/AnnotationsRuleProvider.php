<?php

/*
 * This file is part of the Arachne package.
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Annotations;

use Arachne\Verifier\Exception\InvalidArgumentException;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\RuleProviderInterface;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class AnnotationsRuleProvider implements RuleProviderInterface
{
    /** @var Reader */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty $reflection
     *
     * @return RuleInterface[]
     */
    public function getRules(Reflector $reflection)
    {
        if ($reflection instanceof ReflectionMethod) {
            $rules = $this->reader->getMethodAnnotations($reflection);
        } elseif ($reflection instanceof ReflectionClass) {
            $rules = $this->reader->getClassAnnotations($reflection);
        } elseif ($reflection instanceof ReflectionProperty) {
            $rules = $this->reader->getPropertyAnnotations($reflection);
        } else {
            throw new InvalidArgumentException('Reflection must be an instance of either ReflectionMethod, ReflectionClass or ReflectionProperty.');
        }

        return array_filter($rules, function ($annotation) {
            return $annotation instanceof RuleInterface;
        });
    }
}
