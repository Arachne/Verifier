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
	 * @param \Arachne\Verifier\IRule $rule
	 * @param string $presenter
	 * @param array $parameters
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function checkAnnotation(IRule $rule, $presenter, array $parameters);

}
