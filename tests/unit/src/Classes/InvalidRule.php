<?php

namespace Tests\Unit\Classes;

use Arachne\Verifier\IRule;
use Nette\Object;

/**
 * @author Jáchym Toušek
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class InvalidRule extends Object implements IRule
{
}
