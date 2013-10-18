<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

use \InvalidArgumentException as BaseInvalidArgumentException;

/**
 * The exception that is thrown when an argument does not match with the expected value.
 *
 * @author Jáchym Toušek
 */
class InvalidArgumentException extends BaseInvalidArgumentException
{
}
