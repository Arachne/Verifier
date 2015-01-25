<?php

namespace Tests\Unit\Classes;

use Arachne\Verifier\RuleInterface;
use Nette\Object;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class TestRule extends Object implements RuleInterface
{
}
