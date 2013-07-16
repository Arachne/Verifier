<?php

namespace Tests\Arachne\Verifier;

use Mockery;

final class VerifierTest extends BaseTest
{

	/** @var \Arachne\Verifier\Verifier */
	private $verifier;

	/** @var \Mockery\MockInterface */
	private $container;

	/** @var \Mockery\MockInterface */
	private $presenterFactory;

	protected function _before()
	{
		parent::_before();

		$this->presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory');

		$reader = new \Doctrine\Common\Annotations\AnnotationReader();

		$this->container = Mockery::mock('Nette\DI\Container');

		$this->verifier = new \Arachne\Verifier\Verifier($reader, $this->container, $this->presenterFactory);
	}

	/**
	 * @param \Nette\Application\Request $request
	 * @param int $annotationCount
	 */
	private function setupContainer(\Nette\Application\Request $request, $annotationCount)
	{
		$this->container
				->shouldReceive('getByType')
				->with('TestHandler')
				->once()
				->andReturnUsing(function () use ($request, $annotationCount) {
					$ruleCheck = function ($rule) {
						return $rule instanceof \Tests\TestAnnotation;
					};
					return Mockery::mock('Arachne\Verifier\IAnnotationHandler')
							->shouldReceive('checkAnnotation')
							->times($annotationCount)
							->with(Mockery::on($ruleCheck), $request)
							->andReturnNull()
							->getMock();
				});
	}

	public function testCheckAnnotationsOnClass()
	{
		$reflection = new \ReflectionClass('Tests\TestPresenter');
		$request = new \Nette\Application\Request('', 'GET', []);

		$this->setupContainer($request, 1);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	public function testCheckAnnotationsOnMethod()
	{
		$reflection = new \ReflectionMethod('Tests\TestPresenter', 'actionAction');
		$request = new \Nette\Application\Request('', 'GET', []);

		$this->setupContainer($request, 2);

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	/**
	 * @expectedException \Arachne\Verifier\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.
	 */
	public function testCheckAnnotationsOnProperty()
	{
		$reflection = new \ReflectionProperty('Tests\TestPresenter', 'property');
		$request = new \Nette\Application\Request('', 'GET', []);

		$this->container
				->shouldReceive('getByType')
				->never();

		$this->verifier->checkAnnotations($reflection, $request);
	}

}
