<?php

/**
 * This file is part of the Arachne
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
use Nette\Application\Request;
use Nette\Object;
use ReflectionClass;
use ReflectionMethod;
use Reflector;

/**
 * @author Jáchym Toušek
 */
class AnnotationsRuleProvider extends Object implements RuleProviderInterface
{

	/** @var Reader */
	private $reader;

	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * @param ReflectionClass|ReflectionMethod $rules
	 * @return RuleInterface[]
	 */
	public function getRules(Reflector $reflection)
	{
		if ($reflection instanceof ReflectionMethod) {
			$rules = $this->reader->getMethodAnnotations($reflection);
		} elseif ($reflection instanceof ReflectionClass) {
			$rules = $this->reader->getClassAnnotations($reflection);
		} else {
			throw new InvalidArgumentException('Reflection must be an instance of either ReflectionMethod or ReflectionClass.');
		}
		return array_filter($rules, function ($annotation) {
			return $annotation instanceof RuleInterface;
		});
	}

}