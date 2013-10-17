<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

/**
 * @author Jáchym Toušek
 */
interface IAnnotationHandlerLoader
{

	/**
	 * @param string $type
	 * @return IAnnotationHandler|NULL
	 */
	public function getAnnotationHandler($type);

}
