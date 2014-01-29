<?php

namespace Tests\Unit;

use Arachne\Verifier\IRule;
use Nette\Object;

/**
 * @author Jáchym Toušek
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class TestRule extends Object implements IRule
{
}
