<?php

namespace Tests\Unit;

use Arachne\Verifier\IAnnotation;
use Nette\Object;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class TestAnnotation extends Object implements IAnnotation
{
}
