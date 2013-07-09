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
class Allowed extends \Nette\Object implements \Arachne\Verifier\IRule
{

	/** @var string */
	public $resource = \Nette\Security\IAuthorizator::ALL;

	/** @var string */
	public $privilege = \Nette\Security\IAuthorizator::ALL;

	public function getHandlerClass()
	{
		return 'Arachne\Verifier\Security\SecurityAnnotationHandler';
	}

}
