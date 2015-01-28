<?php

namespace Tests\Unit\Classes;

use Arachne\Verifier\Rules\ValidationRule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class InvalidRule extends ValidationRule
{
}
