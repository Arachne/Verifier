<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Arachne\Verifier\Exception\ForbiddenRequestException;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek
 */
interface IAnnotationHandler
{

	/**
	 * @param IAnnotation $annotation
	 * @param Request $request
	 * @throws ForbiddenRequestException
	 */
	public function checkAnnotation(IAnnotation $annotation, Request $request);

}
