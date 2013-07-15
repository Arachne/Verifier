<?php

namespace Tests;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class TestAnnotation extends \Nette\Object implements \Arachne\Verifier\IRule
{

	public function getHandlerClass()
	{
		return 'TestHandler';
	}

}
