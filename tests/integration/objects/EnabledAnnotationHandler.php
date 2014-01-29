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
	 * @param string $component
	 * @throws DisabledException
	 */
	public function checkAnnotation(IAnnotation $annotation, Request $request, $component = NULL)
	{
		if ($annotation instanceof Enabled) {
			$this->checkAnnotationEnabled($annotation, $request, $component);
		} else {
			throw new \InvalidArgumentException('Unknown annotation \'' . get_class($annotation) . '\' given.');
		}
	}

	/**
	 * @param Allowed $annotation
	 * @throws DisabledException
	 */
	protected function checkAnnotationEnabled(Enabled $annotation, Request $request, $component = NULL)
	{
		if (is_string($annotation->value)) {
			$parameters = $request->getParameters();
			$enabled = (bool) $parameters[$component . '-' . ltrim($annotation->value, '$')];
		} else {
			$enabled = $annotation->value;
		}
		if (!$enabled) {
			throw new DisabledException("This action is not enabled.");
		}
	}

}
