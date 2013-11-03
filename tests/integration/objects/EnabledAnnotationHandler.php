<?php

namespace Tests\Integration;

use Arachne\Verifier\IAnnotation;
use Arachne\Verifier\IAnnotationHandler;
use Nette\Application\Request;
use Nette\Object;

/**
 * @author Jáchym Toušek
 */
class EnabledAnnotationHandler extends Object implements IAnnotationHandler
{

	/**
	 * @param IAnnotation $annotation
	 * @param Request $request
	 * @throws DisabledException
	 */
	public function checkAnnotation(IAnnotation $annotation, Request $request)
	{
		if ($annotation instanceof Enabled) {
			$this->checkAnnotationEnabled($annotation);
		} else {
			throw new \InvalidArgumentException('Unknown annotation \'' . get_class($annotation) . '\' given.');
		}
	}

	/**
	 * @param Allowed $annotation
	 * @throws DisabledException
	 */
	protected function checkAnnotationEnabled(Enabled $annotation)
	{
		if (!$annotation->value) {
			throw new DisabledException("This action is not enabled.");
		}
	}

}
