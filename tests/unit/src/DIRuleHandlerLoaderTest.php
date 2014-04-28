<?php

namespace Tests\Unit;

use Arachne\Verifier\DIRuleHandlerLoader;
use Codeception\TestCase\Test;
use Mockery;
use Mockery\MockInterface;

/**
 * @author Jáchym Toušek
 */
class DIRuleHandlerLoaderTest extends Test
{

	/** @var DIRuleHandlerLoaderTest */
	private $handlerLoader;

	/** @var MockInterface */
	private $container;

	protected function _before()
	{
		$this->container = Mockery::mock('Nette\DI\Container');
		$this->handlerLoader = new DIRuleHandlerLoader([
			'Type1' => 'type1Handler',
		], $this->container);
	}

	public function testHandler()
	{
		$mock = Mockery::mock('Arachne\Verifier\IRuleHandler');
		$this->container
			->shouldReceive('getService')
			->once()
			->with('type1Handler')
			->andReturn($mock);
		$this->assertSame($mock, $this->handlerLoader->getRuleHandler('Type1'));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\UnexpectedTypeException
	 * @expectedExceptionMessage Service 'type1Handler' is not an instance of Arachne\Verifier\IRuleHandler.
	 */
	public function testHandlerWrongClass()
	{
		$this->container
			->shouldReceive('getService')
			->once()
			->with('type1Handler')
			->andReturn(Mockery::mock());
		$this->handlerLoader->getRuleHandler('Type1');
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\UnexpectedTypeException
	 * @expectedExceptionMessage No rule handler found for type 'Type2'.
	 */
	public function testHandlerNotFound()
	{
		$this->handlerLoader->getRuleHandler('Type2');
	}

}
