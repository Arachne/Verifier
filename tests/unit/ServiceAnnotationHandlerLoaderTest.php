<?php

namespace Tests\Unit;

use Arachne\Verifier\ServiceAnnotationHandlerLoader;
use Codeception\TestCase\Test;
use Mockery;
use Mockery\MockInterface;

/**
 * @author Jáchym Toušek
 */
class ServiceAnnotationHandlerLoaderTest extends Test
{

	/** @var ServiceAnnotationHandlerLoader */
	private $handlerLoader;

	/** @var MockInterface */
	private $container;

	protected function _before()
	{
		$this->container = Mockery::mock('Nette\DI\Container');
		$this->handlerLoader = new ServiceAnnotationHandlerLoader([
			'Type1' => 'type1Handler',
		], $this->container);
	}

	public function testHandler()
	{
		$mock = Mockery::mock('Arachne\Verifier\IAnnotationHandler');
		$this->container
			->shouldReceive('getService')
			->once()
			->with('type1Handler')
			->andReturn($mock);
		$this->assertSame($mock, $this->handlerLoader->getAnnotationHandler('Type1'));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\UnexpectedTypeException
	 * @expectedExceptionMessage Service 'type1Handler' is not an instance of Arachne\Verifier\IAnnotationHandler.
	 */
	public function testHandlerWrongClass()
	{
		$this->container
			->shouldReceive('getService')
			->once()
			->with('type1Handler')
			->andReturn(Mockery::mock());
		$this->handlerLoader->getAnnotationHandler('Type1');
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\UnexpectedTypeException
	 * @expectedExceptionMessage No annotation handler found for type 'Type2'.
	 */
	public function testHandlerNotFound()
	{
		$this->handlerLoader->getAnnotationHandler('Type2');
	}

}
