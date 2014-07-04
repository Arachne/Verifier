<?php

namespace Tests\Unit;

use Arachne\Verifier\IRuleHandler;
use Arachne\Verifier\IRuleHandlerLoader;
use Arachne\Verifier\IRuleProvider;
use Arachne\Verifier\Verifier;
use Codeception\TestCase\Test;
use Mockery;
use Mockery\Matcher\MatcherAbstract;
use Mockery\MockInterface;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\PresenterComponent;
use ReflectionClass;
use ReflectionMethod;
use Reflector;
use Tests\Unit\Classes\TestControl;
use Tests\Unit\Classes\TestException;
use Tests\Unit\Classes\TestPresenter;
use Tests\Unit\Classes\TestRule;

/**
 * @author Jáchym Toušek
 */
class VerifierTest extends Test
{

	/** @var Verifier */
	private $verifier;

	/** @var MockInterface */
	private $ruleProvider;

	/** @var MockInterface */
	private $handlerLoader;

	/** @var MockInterface */
	private $presenterFactory;

	protected function _before()
	{
		$this->ruleProvider = Mockery::mock(IRuleProvider::class);
		$this->handlerLoader = Mockery::mock(IRuleHandlerLoader::class);
		$this->presenterFactory = Mockery::mock(IPresenterFactory::class);
		$this->verifier = new Verifier([ $this->ruleProvider ], $this->handlerLoader, $this->presenterFactory);
	}

	public function testCheckRulesOnClass()
	{
		$reflection = Mockery::mock(ReflectionClass::class);
		$reflection
			->shouldReceive('getName')
			->twice()
			->andReturn('class');
		$request = Mockery::mock(Request::class);
		$handler = $this->createHandlerMock($request, 2);

		$this->setupRuleProviderMock($reflection, 1);
		$this->setupHandlerLoaderMock($handler, 2);

		$this->verifier->checkReflection($reflection, $request);
		$this->verifier->checkReflection($reflection, $request);
	}

	public function testCheckRulesOnMethod()
	{
		$reflection = Mockery::mock(ReflectionMethod::class);
		$classReflection = Mockery::mock(ReflectionClass::class);
		$classReflection->shouldReceive('getName')
			->twice()
			->andReturn('class');
		$reflection
			->shouldReceive('getName')
			->twice()
			->andReturn('method')
			->shouldReceive('getDeclaringClass')
			->twice()
			->andReturn($classReflection);
		$request = Mockery::mock(Request::class);
		$handler = $this->createHandlerMock($request, 2);

		$this->setupRuleProviderMock($reflection, 1);
		$this->setupHandlerLoaderMock($handler, 2);

		$this->verifier->checkReflection($reflection, $request);
		$this->verifier->checkReflection($reflection, $request);
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either ReflectionMethod or ReflectionClass.
	 */
	public function testCheckRulesOnProperty()
	{
		$reflection = Mockery::mock(Reflector::class);
		$request = Mockery::mock(Request::class);
		$this->verifier->checkReflection($reflection, $request);
	}

	public function testIsLinkVerifiedTrue()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'action',
		]);
		$handler = $this->createHandlerMock($request, 3);

		$this->setupRuleProviderMock(Mockery::type(ReflectionMethod::class), 2);
		$this->setupRuleProviderMock(Mockery::type(ReflectionClass::class), 1);
		$this->setupHandlerLoaderMock($handler, 3);
		$this->setupPresenterFactoryMock();

		$this->assertTrue($this->verifier->isLinkVerified($request, Mockery::mock(PresenterComponent::class)));
	}

	public function testIsLinkVerifiedFalse()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'view',
		]);
		$handler = Mockery::mock(IRuleHandler::class)
			->shouldReceive('checkRule')
			->once()
			->with(Mockery::type(TestRule::class), $request, NULL)
			->andThrow(TestException::class)
			->getMock();

		$this->setupRuleProviderMock(Mockery::type(ReflectionClass::class), 1);
		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();

		$this->assertFalse($this->verifier->isLinkVerified($request, Mockery::mock(PresenterComponent::class)));
	}

	public function testIsLinkVerifiedSignal()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'signal',
		]);
		$handler = $this->createHandlerMock($request, 1, 'test-component');

		$this->setupRuleProviderMock(Mockery::type(ReflectionMethod::class), 1);
		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();

		$component = Mockery::mock(PresenterComponent::class)
			->shouldReceive('getName')
			->once()
			->andReturn('test-component')
			->shouldReceive('getParent')
			->once()
			->andReturn(Mockery::mock(PresenterComponent::class))
			->getMock();

		$this->assertTrue($this->verifier->isLinkVerified($request, $component));
	}

	public function testIsComponentVerifiedTrue()
	{
		$request = Mockery::mock(Request::class);
		$handler = $this->createHandlerMock($request, 1);

		$this->setupRuleProviderMock(Mockery::type(ReflectionMethod::class), 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$parent = new TestPresenter();
		$parent->setParent($parent, 'Test');

		$this->assertTrue($this->verifier->isComponentVerified('component', $request, $parent));
	}

	public function testIsComponentVerifiedFalse()
	{
		$request = Mockery::mock(Request::class);
		$handler = Mockery::mock(IRuleHandler::class)
			->shouldReceive('checkRule')
			->once()
			->with(Mockery::type(TestRule::class), $request, NULL)
			->andThrow(TestException::class)
			->getMock();

		$this->setupRuleProviderMock(Mockery::type(ReflectionMethod::class), 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$parent = new TestPresenter();

		$this->assertFalse($this->verifier->isComponentVerified('component', $request, $parent));
	}

	public function testIsComponentSignalVerifiedTrue()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'component-signal',
		], FALSE);
		$handler = $this->createHandlerMock($request, 1, 'component');

		$this->setupRuleProviderMock(Mockery::type(ReflectionMethod::class), 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$component = new TestControl(NULL, 'component');
		$parent = Mockery::mock(PresenterComponent::class)
			->shouldDeferMissing();
		$component->setParent($parent);

		$this->assertTrue($this->verifier->isLinkVerified($request, $component));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Wrong signal receiver, expected 'component' component but 'test-component' was given.
	 */
	public function testWrongSignalReceiver()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'component-signal',
		], FALSE);

		$component = new TestControl(NULL, 'test-component');

		$this->verifier->isLinkVerified($request, $component);
	}

	/**
	 * @param Request $request
	 * @param int $limit
	 * @param string $component
	 * @return IRuleHandler
	 */
	private function createHandlerMock(Request $request, $limit, $component = NULL)
	{
		return Mockery::mock(IRuleHandler::class)
			->shouldReceive('checkRule')
			->times($limit)
			->with(Mockery::type(TestRule::class), $request, $component)
			->andReturnNull()
			->getMock();
	}

	/**
	 * @param array $parameters
	 * @param bool $presenter
	 * @return Request
	 */
	private function createRequestMock(array $parameters, $presenter = TRUE)
	{
		$request = Mockery::mock(Request::class);
		$request->shouldReceive('getParameters')
			->once()
			->andReturn($parameters);
		if ($presenter) {
			$request->shouldReceive('getPresenterName')
				->once()
				->andReturn('Test');
		}
		return $request;
	}

	/**
	 * @param IRuleHandler $handler
	 * @param int $limit
	 */
	private function setupHandlerLoaderMock(IRuleHandler $handler, $limit)
	{
		$this->handlerLoader
			->shouldReceive('getRuleHandler')
			->with(TestRule::class)
			->times($limit)
			->andReturn($handler);
	}

	/**
	 * @param Reflector|MatcherAbstract $matcher
	 * @param int $limit
	 */
	private function setupRuleProviderMock($matcher, $limit)
	{
		$this->ruleProvider
			->shouldReceive('getRules')
			->times($limit)
			->with($matcher)
			->andReturn([ new TestRule() ]);
	}

	private function setupPresenterFactoryMock()
	{
		$this->presenterFactory
			->shouldReceive('getPresenterClass')
			->with('Test')
			->once()
			->andReturn(TestPresenter::class);
	}

}
