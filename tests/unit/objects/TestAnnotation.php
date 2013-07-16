<?php

namespace Tests;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class TestAnnotation extends \Nette\Object implements \Arachne\Verifier\IAnnotation
{

	public function getHandlerClass()
	{
		return 'TestHandler';
	}

}
