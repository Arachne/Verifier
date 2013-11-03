<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Exception;

use Nette\Application\ForbiddenRequestException as BaseForbiddenRequestException;

/**
 * Descendants of this exception are to be thrown by annotation handlers.
 *
 * @author Jáchym Toušek
 */
abstract class ForbiddenRequestException extends BaseForbiddenRequestException
{
}
