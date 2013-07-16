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
	 * @param \Arachne\Verifier\ICondition $condition
	 * @param \Nette\Application\Request $request
	 * @throws \Arachne\Verifier\ForbiddenRequestException
	 */
	public function checkAnnotation(ICondition $condition, \Nette\Application\Request $request);

}
