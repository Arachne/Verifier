<?php

namespace Tests\Integration\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ChildControl extends Control
{

	use VerifierControlTrait;

	/**
	 * @Enabled( "$parameter" )
	 */
	public function handleSignal1($parameter)
	{
		$this->redirectVerified('signal2!');
	}

	public function handleSignal2()
	{
	}

}
