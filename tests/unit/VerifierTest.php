<?php

namespace Tests\Unit;

use Arachne\Verifier\IRuleHandler;
use Arachne\Verifier\Verifier;
use Codeception\TestCase\Test;
use Mockery;
use Mockery\Matcher\MatcherAbstract;
use Mockery\MockInterface;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Reflector;

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
		$this->ruleProvider = Mockery::mock('Arachne\Verifier\IRuleProvider');
		$this->handlerLoader = Mockery::mock('Arachne\Verifier\IRuleHandlerLoader');
		$this->presenterFactory = Mockery::mock('Nette\Application\IPresenterFactory');
		$this->verifier = new Verifier([ $this->ruleProvider ], $this->handlerLoader, $this->presenterFactory);
	}

	public function testCheckRulesOnClass()
	{
		$reflection = Mockery::mock('ReflectionClass');
		$request = Mockery::mock('Nette\Application\Request');
		$handler = $this->createHandlerMock($request, 1);

		$this->setupRuleProviderMock($reflection, $request, 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$this->verifier->checkRules($reflection, $request);
	}

	public function testCheckRulesOnMethod()
	{
		$reflection = Mockery::mock('ReflectionMethod');
		$request = Mockery::mock('Nette\Application\Request');
		$handler = $this->createHandlerMock($request, 1);

		$this->setupRuleProviderMock($reflection, $request, 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$this->verifier->checkRules($reflection, $request);
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either ReflectionMethod or ReflectionClass.
	 */
	public function testCheckRulesOnProperty()
	{
		$reflection = Mockery::mock('Reflector');
		$request = Mockery::mock('Nette\Application\Request');
		$this->verifier->checkRules($reflection, $request);
	}

	public function testIsLinkVerifiedTrue()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'action',
		]);
		$handler = $this->createHandlerMock($request, 3);

		$this->setupRuleProviderMock(Mockery::type('ReflectionMethod'), $request, 2);
		$this->setupRuleProviderMock(Mockery::type('ReflectionClass'), $request, 1);
		$this->setupHandlerLoaderMock($handler, 3);
		$this->setupPresenterFactoryMock();

		$this->assertTrue($this->verifier->isLinkVerified($request, Mockery::mock('Nette\Application\UI\PresenterComponent')));
	}

	public function testIsLinkVerifiedFalse()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'view',
		]);
		$handler = Mockery::mock('Arachne\Verifier\IRuleHandler')
			->shouldReceive('checkRule')
			->once()
			->with(Mockery::type('Tests\Unit\TestRule'), $request, NULL)
			->andThrow('Tests\Unit\TestException')
			->getMock();

		$this->setupRuleProviderMock(Mockery::type('ReflectionClass'), $request, 1);
		$this->setupHandlerLoaderMock($handler, 1);
		$this->setupPresenterFactoryMock();

		$this->assertFalse($this->verifier->isLinkVerified($request, Mockery::mock('Nette\Application\UI\PresenterComponent')));
	}

	public function testIsLinkVerifiedSignal()
	{
		$request = $this->createRequestMock([
			Presenter::ACTION_KEY => 'action',
			Presenter::SIGNAL_KEY => 'signal',
		]);
		$handler = $this->createHandlerMock($request, 1, 'test-component');

		$this->setupRuleProviderMock(Mockery::type('ReflectionMethod'), $request, 1, 'test-component');
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
		$request = Mockery::mock('Nette\Application\Request');
		$handler = $this->createHandlerMock($request, 1);

		$this->setupRuleProviderMock(Mockery::type('ReflectionMethod'), $request, 1);
		$this->setupHandlerLoaderMock($handler, 1);

		$parent = new TestPresenter();
		$parent->setParent($parent, 'Test');

		$this->assertTrue($this->verifier->isComponentVerified('component', $request, $parent));
	}

	public function testIsComponentVerifiedFalse()
	{
		$request = Mockery::mock('Nette\Application\Request');
		$handler = Mockery::mock('Arachne\Verifier\IRuleHandler')
			->shouldReceive('checkRule')
			->once()
			->with(Mockery::type('Tests\Unit\TestRule'), $request, NULL)
			->andThrow('Tests\Unit\TestException')
			->getMock();

		$this->setupRuleProviderMock(Mockery::type('ReflectionMethod'), $request, 1);
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

		$this->setupRuleProviderMock(Mockery::type('ReflectionMethod'), $request, 1, 'component');
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
		return Mockery::mock('Arachne\Verifier\IRuleHandler')
			->shouldReceive('checkRule')
			->times($limit)
			->with(Mockery::type('Tests\Unit\TestRule'), $request, $component)
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
		$request = Mockery::mock('Nette\Application\Request');
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
			->with('Tests\Unit\TestRule')
			->times($limit)
			->andReturn($handler);
	}

	/**
	 * @param Reflector|MatcherAbstract $matcher
	 * @param Request $request
	 * @param int $limit
	 */
	private function setupRuleProviderMock($matcher, Request $request, $limit, $component = '')
	{
		$this->ruleProvider
			->shouldReceive('getRules')
			->times($limit)
			->with($matcher, $request, $component)
			->andReturn([ new TestRule() ]);
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
