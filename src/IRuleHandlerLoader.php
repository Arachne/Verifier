<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

/**
 * @author Jáchym Toušek
 */
interface IRuleHandlerLoader
{

	/**
	 * @param string $type
	 * @return IRuleHandler
	 */
	public function getRuleHandler($type);

}
