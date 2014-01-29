<?php

namespace Tests\Unit;

use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek
 *
 * @TestAnnotation
 */
class TestControl extends Control
{

	/**
	 * @TestAnnotation
	 */
	public function handleSignal()
	{
	}

	/**
	 * @TestAnnotation
	 */
	public function createComponentComponent()
	{
	}

}
