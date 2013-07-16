<?php

namespace Tests\Arachne\Verifier;

abstract class BaseTest extends \Codeception\TestCase\Test
{

	protected function _before()
	{
	}

	protected function _after()
	{
		\Mockery::close();
	}

}
