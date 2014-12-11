<?php

namespace Tests\Integration\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek
 */
class BlockControl extends Control
{

	use VerifierControlTrait;

	public function render()
	{
		$this->template->setFile(__DIR__ . '/../../templates/block.latte');
		$this->template->render();
	}

	/**
	 * @Enabled( "$parameter" )
	 */
	public function handleSignal($parameter)
	{
		$this->template->message = 'Signal called!';
	}

}
