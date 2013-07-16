<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

interface IAnnotationHandler
{

	/**
	 * @param \Arachne\Verifier\IAnnotation $annotation
	 * @param \Nette\Application\Request $request
	 * @throws \Arachne\Verifier\ForbiddenRequestException
	 */
	public function checkAnnotation(IAnnotation $annotation, \Nette\Application\Request $request);

}
