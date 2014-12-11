<?php

namespace Tests\Integration\Classes;

use Arachne\Verifier\RuleInterface;
use Nette\Object;

/**
 * @author Jáchym Toušek
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Enabled extends Object implements RuleInterface
{

	public $value;

}
