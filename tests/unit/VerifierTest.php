<?php

namespace Tests\Unit;

use Arachne\Verifier\IAnnotationHandler;
use Arachne\Verifier\Verifier;
use Doctrine\Common\Annotations\AnnotationReader;
use Mockery;
use Mockery\MockInterface;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class VerifierTest extends BaseTest
{

	/** @var Verifier */
	private $verifier;

	/** @var MockInterface */
	private $container;

	/** @var MockInterface */
	private $presenterFactory;

	protected function _before()
	{
		$reader = new AnnotationReader();
		$this->container = Mockery::mock('Nette\DI\Container');
		$this->presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory');
		$this->verifier = new Verifier($reader, $this->container, $this->presenterFactory);
	}

	public function testCheckAnnotationsOnClass()
	{
		$reflection = new ReflectionClass('Tests\Unit\TestPresenter');
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 1);
		$this->setupContainerMock($handler);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	public function testCheckAnnotationsOnMethod()
	{
		$reflection = new ReflectionMethod('Tests\Unit\TestPresenter', 'renderView');
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 2);
		$this->setupContainerMock($handler);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	/**
	 * @expectedException Arachne\Verifier\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.
	 */
	public function testCheckAnnotationsOnProperty()
	{
		$reflection = new ReflectionProperty('Tests\Unit\TestPresenter', 'property');
		$request = new Request('Test', 'GET', []);
		$this->verifier->checkAnnotations($reflection, $request);
	}

	public function testIsLinkAvailableTrue()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'signal',
		]);

		$handler = $this->createHandlerMock($request, 4);
		$this->setupContainerMock($handler);
		$this->setupPresenterFactoryMock();

		$this->assertTrue($this->verifier->isLinkAvailable($request));
	}

	public function testIsLinkAvailableFalse()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'view',
		]);

		$handler = Mockery::mock('Arachne\Verifier\IAnnotationHandler')
			->shouldReceive('checkAnnotation')
			->once()
			->with($this->createAnnotationMatcher(), $request)
			->andThrow('Tests\Unit\TestException')
			->getMock();

		$this->setupContainerMock($handler);
		$this->setupPresenterFactoryMock();

		$this->assertFalse($this->verifier->isLinkAvailable($request));
	}

	private function createAnnotationMatcher()
	{
		return Mockery::on(function ($annotation) {
			return $annotation instanceof TestAnnotation;
		});
	}

	/**
	 * @param Request $request
	 * @param int $times
	 */
	private function createHandlerMock(Request $request, $times)
	{
		return Mockery::mock('Arachne\Verifier\IAnnotationHandler')
			->shouldReceive('checkAnnotation')
			->times($times)
			->with($this->createAnnotationMatcher(), $request)
			->andReturnNull()
			->getMock();
	}

	/**
	 * @param IAnnotationHandler $handler
	 */
	private function setupContainerMock(IAnnotationHandler $handler)
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
			->andReturn('Tests\Unit\TestPresenter');
	}

}
