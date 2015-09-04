<?php

namespace Tests\Integration\Classes;

use Arachne\Verifier\Rules\SecurityRule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
class Enabled extends SecurityRule
{

	public $value;

}
