<?php

namespace Tests\Arachne\Verifier;

abstract class BaseTest extends \Codeception\TestCase\Test
{

	protected function tearDown()
	{
		\Mockery::close();
		parent::tearDown();
	}

}
