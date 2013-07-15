<?php

namespace Tests\Arachne\Verifier;

use Mockery;

final class VerifierTest extends \Codeception\TestCase\Test
{

	/** @var \Arachne\Verifier\Verifier */
	private $verifier;

	/** @var \Mockery\MockInterface */
	private $handler;

	/** @var \Mockery\MockInterface */
	private $presenterFactory;

	protected function _before()
	{
		$this->presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory');

		$reader = new \Doctrine\Common\Annotations\AnnotationReader();

		$container = Mockery::mock('Nette\DI\Container')
				->shouldReceive('getByType')
				->with('TestHandler')
				->once()
				->andReturnUsing(function () {
					$this->assertInstanceOf('Arachne\Verifier\IAnnotationHandler', $this->handler);
					return $this->handler;
				})
				->getMock();

		$this->verifier = new \Arachne\Verifier\Verifier($this->presenterFactory, $reader, $container);
	}

	protected function _after()
	{
		Mockery::close();
	}

	public function testCheckAnnotationsOnClass()
	{
		$reflection = new \ReflectionClass('Tests\TestPresenter');

		$request = new \Nette\Application\Request('', 'GET', []);

		$ruleCheck = function ($rule) {
			return $rule instanceof \Tests\TestAnnotation;
		};
		$this->handler = Mockery::mock('Arachne\Verifier\IAnnotationHandler')
				->shouldReceive('checkAnnotation')
				->once()
				->with(Mockery::on($ruleCheck), $request)
				->andReturnNull()
				->getMock();

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	public function testCheckAnnotationsOnMethod()
	{
		$reflection = new \ReflectionMethod('Tests\TestPresenter', 'actionAction');

		$request = new \Nette\Application\Request('', 'GET', []);

		$ruleCheck = function ($rule) {
			return $rule instanceof \Tests\TestAnnotation;
		};
		$this->handler = Mockery::mock('Arachne\Verifier\IAnnotationHandler')
				->shouldReceive('checkAnnotation')
				->twice()
				->with(Mockery::on($ruleCheck), $request)
				->andReturnNull()
				->getMock();

		$this->assertNull($this->verifier->checkAnnotations($reflection, $request));
	}

	/**
	 * @expectedException \Arachne\Verifier\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.
	 */
	/*public function testCheckAnnotationsOnProperty()
	{
		$reflection = new \ReflectionProperty('Tests\TestPresenter', 'property');

		$request = new \Nette\Application\Request('', 'GET', []);

		$this->verifier->checkAnnotations($reflection, $request);
	}*/

}
