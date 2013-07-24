<?php

namespace Tests;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class TestAnnotation extends \Nette\Object implements \Arachne\Verifier\IAnnotation
{

	public function getHandlerClass()
	{
		return 'TestHandler';
	}

}
