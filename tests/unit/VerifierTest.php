<?php

namespace Tests\Arachne\Verifier;

use Mockery;

class VerifierTest extends BaseTest
{

	/** @var \Arachne\Verifier\Verifier */
	private $verifier;

	/** @var \Mockery\MockInterface */
	private $container;

	/** @var \Mockery\MockInterface */
	private $presenterFactory;

	protected function _before()
	{
		$reader = new \Doctrine\Common\Annotations\AnnotationReader();
		$this->container = Mockery::mock('Nette\DI\Container');
		$this->presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory');
		$this->verifier = new \Arachne\Verifier\Verifier($reader, $this->container, $this->presenterFactory);
	}

	public function testCheckAnnotationsOnClass()
	{
		$reflection = new \ReflectionClass('Tests\TestPresenter');
		$request = new \Nette\Application\Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 1);
		$this->setupContainerMock($handler);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	public function testCheckAnnotationsOnMethod()
	{
		$reflection = new \ReflectionMethod('Tests\TestPresenter', 'renderView');
		$request = new \Nette\Application\Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 2);
		$this->setupContainerMock($handler);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	/**
	 * @expectedException \Arachne\Verifier\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.
	 */
	public function testCheckAnnotationsOnProperty()
	{
		$reflection = new \ReflectionProperty('Tests\TestPresenter', 'property');
		$request = new \Nette\Application\Request('Test', 'GET', []);
		$this->verifier->checkAnnotations($reflection, $request);
	}

	public function testIsLinkAvailableTrue()
	{
		$request = new \Nette\Application\Request('Test', 'GET', [
			\Nette\Application\UI\Presenter::ACTION_KEY => 'action',
			\Nette\Application\UI\Presenter::SIGNAL_KEY => 'signal',
		]);

		$handler = $this->createHandlerMock($request, 4);
		$this->setupContainerMock($handler);
		$this->setupPresenterFactoryMock();

		$this->assertTrue($this->verifier->isLinkAvailable($request));
	}

	public function testIsLinkAvailableFalse()
	{
		$request = new \Nette\Application\Request('Test', 'GET', [
			\Nette\Application\UI\Presenter::ACTION_KEY => 'view',
		]);

		$handler = Mockery::mock('Arachne\Verifier\IAnnotationHandler')
				->shouldReceive('checkAnnotation')
				->once()
				->with($this->createAnnotationMatcher(), $request)
				->andThrow('Tests\TestException')
				->getMock();

		$this->setupContainerMock($handler);
		$this->setupPresenterFactoryMock();

		$this->assertFalse($this->verifier->isLinkAvailable($request));
	}

	private function createAnnotationMatcher()
	{
		return Mockery::on(function ($annotation) {
			return $annotation instanceof \Tests\TestAnnotation;
		});
	}

	/**
	 * @param \Nette\Application\Request $request
	 * @param int $times
	 */
	private function createHandlerMock(\Nette\Application\Request $request, $times)
	{
		return Mockery::mock('Arachne\Verifier\IAnnotationHandler')
				->shouldReceive('checkAnnotation')
				->times($times)
				->with($this->createAnnotationMatcher(), $request)
				->andReturnNull()
				->getMock();
	}

	/**
	 * @param \Arachne\Verifier\IAnnotationHandler $handler
	 */
	private function setupContainerMock(\Arachne\Verifier\IAnnotationHandler $handler)
	{
		$this->container
				->shouldReceive('getByType')
				->with('TestHandler')
				->once()
				->andReturn($handler);
	}

	private function setupPresenterFactoryMock()
	{
		$this->presenterFactory
				->shouldReceive('getPresenterClass')
				->with('Test')
				->once()
				->andReturn('Tests\TestPresenter');
	}

}
