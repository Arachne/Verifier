<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Security;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class InRole extends \Nette\Object implements \Arachne\Verifier\IRule
{

	/** @var string */
	public $role;

	public function getHandlerClass()
	{
		return 'Arachne\Verifier\Security\SecurityAnnotationHandler';
	}

}
