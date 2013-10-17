<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use Nette\Application\ForbiddenRequestException as BaseForbiddenRequestException;

/**
 * Descendants of this exception are to be thrown by annotation handlers.
 */
abstract class ForbiddenRequestException extends BaseForbiddenRequestException
{
}
