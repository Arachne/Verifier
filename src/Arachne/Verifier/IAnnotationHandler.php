<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Nette\Application\BadRequestException;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek
 */
interface IAnnotationHandler
{

	/**
	 * @param IAnnotation $annotation
	 * @param Request $request
	 * @throws BadRequestException
	 */
	public function checkAnnotation(IAnnotation $annotation, Request $request);

}
