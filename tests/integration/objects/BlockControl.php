<?php

namespace Tests\Integration;

use Arachne\Verifier\Application\TVerifierControl;
use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek
 */
class BlockControl extends Control
{

	use TVerifierControl;

	public function render()
	{
		$this->template->setFile(__DIR__ . '/../templates/block.latte');
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
