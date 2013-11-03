<?php

namespace Tests\Integration;

use Arachne\Verifier\IAnnotation;
use Nette\Object;

/**
 * @author Jáchym Toušek
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Enabled extends Object implements IAnnotation
{

	/** @var bool */
	public $value;

}
