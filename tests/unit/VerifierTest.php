<?php

namespace Tests\Arachne\Verifier;

use Mockery;

final class VerifierTest extends \Codeception\TestCase\Test
{

	/** @var \Arachne\Verifier\Verifier */
	private $verifier;

	/** @var \Nette\Security\User */
	private $user;

	protected function _before()
	{
		parent::_before();
		$presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory')
				->shouldReceive('getPresenterClass')
				->once()
				->andReturn('Tests\TestPresenter')
				->getMock();
		$this->user = Mockery::mock('Nette\Security\User');
		$this->verifier = new \Arachne\Verifier\Verifier($presenterFactory, $this->user);
	}

	protected function _after()
	{
		Mockery::close();
	}

	public function testCheckAnnotations()
	{
		
	}

}
