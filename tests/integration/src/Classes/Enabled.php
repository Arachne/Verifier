<?php

namespace Tests\Integration\Classes;

use Arachne\Verifier\Rules\SecurityRule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Enabled extends SecurityRule
{

	public $value;

}
