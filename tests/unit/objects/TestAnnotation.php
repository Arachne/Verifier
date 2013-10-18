<?php

namespace Tests\Unit;

use Arachne\Verifier\IAnnotation;
use Nette\Object;

/**
 * @author Jáchym Toušek
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class TestAnnotation extends Object implements IAnnotation
{
}
