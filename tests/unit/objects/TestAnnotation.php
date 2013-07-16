<?php

namespace Tests;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class TestAnnotation extends \Nette\Object implements \Arachne\Verifier\ICondition
{

	public function getHandlerClass()
	{
		return 'TestHandler';
	}

}
