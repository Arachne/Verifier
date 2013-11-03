<?php

namespace Tests\Unit;

use Arachne\Verifier\IAnnotationHandler;
use Arachne\Verifier\Verifier;
use Codeception\TestCase\Test;
use Doctrine\Common\Annotations\AnnotationReader;
use Mockery;
use Mockery\MockInterface;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * @author Jáchym Toušek
 */
class VerifierTest extends Test
{

	/** @var Verifier */
	private $verifier;

	/** @var MockInterface */
	private $handlerLoader;

	/** @var MockInterface */
	private $presenterFactory;

	protected function _before()
	{
		$reader = new AnnotationReader();
		$this->handlerLoader = Mockery::mock('Arachne\Verifier\IAnnotationHandlerLoader');
		$this->presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory');
		$this->verifier = new Verifier($reader, $this->handlerLoader, $this->presenterFactory);
	}

	public function testCheckAnnotationsOnClass()
	{
		$reflection = new ReflectionClass('Tests\Unit\TestPresenter');
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	public function testCheckAnnotationsOnMethod()
	{
		$reflection = new ReflectionMethod('Tests\Unit\TestPresenter', 'renderView');
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 2);
		$this->setupHandlerLoaderMock($handler, 2);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.
	 */
	public function testCheckAnnotationsOnProperty()
	{
		$reflection = new ReflectionProperty('Tests\Unit\TestPresenter', 'property');
		$request = new Request('Test', 'GET', []);
		$this->verifier->checkAnnotations($reflection, $request);
	}

	public function testIsLinkVerifiedTrue()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'signal',
		]);

		$handler = $this->createHandlerMock($request, 4);
		$this->setupHandlerLoaderMock($handler, 4);
		$this->setupPresenterFactoryMock();

		$this->assertTrue($this->verifier->isLinkVerified($request));
	}

	public function testIsLinkVerifiedFalse()
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

		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();

		$this->assertFalse($this->verifier->isLinkVerified($request));
	}

	public function testIsComponentVerifiedTrue()
	{
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 1);
		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();

		$this->assertTrue($this->verifier->isComponentVerified($request, 'component'));
	}

	public function testIsComponentVerifiedFalse()
	{
		$request = new Request('Test', 'GET', []);

		$handler = Mockery::mock('Arachne\Verifier\IAnnotationHandler')
			->shouldReceive('checkAnnotation')
			->once()
			->with($this->createAnnotationMatcher(), $request)
			->andThrow('Tests\Unit\TestException')
			->getMock();

		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();

		$this->assertFalse($this->verifier->isComponentVerified($request, 'component'));
	}

	private function createAnnotationMatcher()
	{
		return Mockery::on(function ($annotation) {
			return $annotation instanceof TestAnnotation;
		});
	}

	/**
	 * @param Request $request
	 * @param int $limit
	 */
	private function createHandlerMock(Request $request, $limit)
	{
		return Mockery::mock('Arachne\Verifier\IAnnotationHandler')
			->shouldReceive('checkAnnotation')
			->times($limit)
			->with($this->createAnnotationMatcher(), $request)
			->andReturnNull()
			->getMock();
	}

	/**
	 * @param IAnnotationHandler $handler
	 * @param int $limit
	 */
	private function setupHandlerLoaderMock(IAnnotationHandler $handler, $limit)
	{
		$this->handlerLoader
			->shouldReceive('getAnnotationHandler')
			->with('Tests\Unit\TestAnnotation')
			->times($limit)
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
