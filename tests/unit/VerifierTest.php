<?php

namespace Tests\Unit;

use Arachne\Verifier\IRuleHandler;
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
		$this->handlerLoader = Mockery::mock('Arachne\Verifier\IRuleHandlerLoader');
		$this->presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory');
		$this->verifier = new Verifier($reader, $this->handlerLoader, $this->presenterFactory);
	}

	public function testCheckRulesOnClass()
	{
		$reflection = new ReflectionClass('Tests\Unit\TestPresenter');
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$this->assertNull($this->verifier->checkRules($reflection, $request));
	}

	public function testCheckRulesOnMethod()
	{
		$reflection = new ReflectionMethod('Tests\Unit\TestPresenter', 'renderView');
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 2);
		$this->setupHandlerLoaderMock($handler, 2);

		$this->assertNull($this->verifier->checkRules($reflection, $request));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either \ReflectionMethod or \ReflectionClass.
	 */
	public function testCheckRulesOnProperty()
	{
		$reflection = new ReflectionProperty('Tests\Unit\TestPresenter', 'property');
		$request = new Request('Test', 'GET', []);
		$this->verifier->checkRules($reflection, $request);
	}

	public function testIsLinkVerifiedTrue()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'action',
		]);

		$handler = $this->createHandlerMock($request, 3);
		$this->setupHandlerLoaderMock($handler, 3);
		$this->setupPresenterFactoryMock();

		$this->assertTrue($this->verifier->isLinkVerified($request, Mockery::mock('Nette\Application\UI\PresenterComponent')));
	}

	public function testIsLinkVerifiedFalse()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'view',
		]);

		$handler = Mockery::mock('Arachne\Verifier\IRuleHandler')
			->shouldReceive('checkRule')
			->once()
			->with($this->createRuleMatcher(), $request, NULL)
			->andThrow('Tests\Unit\TestException')
			->getMock();

		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();

		$this->assertFalse($this->verifier->isLinkVerified($request, Mockery::mock('Nette\Application\UI\PresenterComponent')));
	}

	public function testIsLinkVerifiedSignal()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'signal',
		]);

		$handler = $this->createHandlerMock($request, 1, 'test-component');
		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();
		$component = Mockery::mock('Nette\Application\UI\PresenterComponent')
			->shouldReceive('getName')
			->once()
			->andReturn('test-component')
			->shouldReceive('getParent')
			->once()
			->getMock();

		$this->assertTrue($this->verifier->isLinkVerified($request, $component));
	}

	public function testIsComponentVerifiedTrue()
	{
		$request = new Request('Test', 'GET', []);

		$handler = $this->createHandlerMock($request, 1);
		$this->setupHandlerLoaderMock($handler, 1);
		$parent = new TestPresenter();
		$parent->setParent($parent, 'Test');

		$this->assertTrue($this->verifier->isComponentVerified('component', $request, $parent));
	}

	public function testIsComponentVerifiedFalse()
	{
		$request = new Request('Test', 'GET', []);

		$handler = Mockery::mock('Arachne\Verifier\IRuleHandler')
			->shouldReceive('checkRule')
			->once()
			->with($this->createRuleMatcher(), $request, NULL)
			->andThrow('Tests\Unit\TestException')
			->getMock();

		$this->setupHandlerLoaderMock($handler, 1);
		$parent = new TestPresenter();

		$this->assertFalse($this->verifier->isComponentVerified('component', $request, $parent));
	}

	public function testIsComponentSignalVerifiedTrue()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'component-signal',
		]);

		$handler = $this->createHandlerMock($request, 1, 'component');
		$this->setupHandlerLoaderMock($handler, 1);
		$component = new TestControl(NULL, 'component');

		$this->assertTrue($this->verifier->isLinkVerified($request, $component));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Wrong signal receiver, expected 'component' component but 'test-component' was given.
	 */
	public function testWrongSignalReceiver()
	{
		$request = new Request('Test', 'GET', [
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'component-signal',
		]);

		$component = new TestControl(NULL, 'test-component');

		$this->verifier->isLinkVerified($request, $component);
	}

	private function createRuleMatcher()
	{
		return Mockery::on(function ($rule) {
			return $rule instanceof TestRule;
		});
	}

	/**
	 * @param Request $request
	 * @param int $limit
	 * @param string $component
	 */
	private function createHandlerMock(Request $request, $limit, $component = NULL)
	{
		return Mockery::mock('Arachne\Verifier\IRuleHandler')
			->shouldReceive('checkRule')
			->times($limit)
			->with($this->createRuleMatcher(), $request, $component)
			->andReturnNull()
			->getMock();
	}

	/**
	 * @param IRuleHandler $handler
	 * @param int $limit
	 */
	private function setupHandlerLoaderMock(IRuleHandler $handler, $limit)
	{
		$this->handlerLoader
			->shouldReceive('getRuleHandler')
			->with('Tests\Unit\TestRule')
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
