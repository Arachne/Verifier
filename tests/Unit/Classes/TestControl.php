<?php

namespace Arachne\Tests\Verifier\Unit\Classes;

use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek
 *
 * @TestRule
 */
class TestControl extends Control
{

	/**
	 * @TestRule
	 */
	public function handleSignal()
	{
	}

	/**
	 * @TestRule
	 */
	public function createComponentComponent()
	{
	}

}