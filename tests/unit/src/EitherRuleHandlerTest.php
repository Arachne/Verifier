<?php

namespace Tests\Unit;

use Arachne\Verifier\Exception\VerificationException;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\Rules\Either;
use Arachne\Verifier\Rules\EitherRuleHandler;
use Arachne\Verifier\Verifier;
use Codeception\MockeryModule\Test;
use Mockery;
use Mockery\MockInterface;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class EitherRuleHandlerTest extends Test
{

	/** @var EitherRuleHandler */
	private $handler;

	/** @var MockInterface */
	private $verifier;

	protected function _before()
	{
		$this->verifier = Mockery::mock(Verifier::class);
		$this->handler = new EitherRuleHandler($this->verifier);
	}

	public function testEitherFirst()
	{
		$rule = new Either();
		$rule->rules = [
			$rule1 = Mockery::mock(RuleInterface::class),
			$rule2 = Mockery::mock(RuleInterface::class),
		];
		$request = new Request('Test', 'GET', []);

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule1 ], $request, null)
			->once()
			->andReturn();

		$this->assertNull($this->handler->checkRule($rule, $request));
	}

	public function testEitherSecond()
	{
		$rule = new Either();
		$rule->rules = [
			$rule1 = Mockery::mock(RuleInterface::class),
			$rule2 = Mockery::mock(RuleInterface::class),
		];
		$request = new Request('Test', 'GET', []);

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule1 ], $request, null)
			->once()
			->andThrow(Mockery::mock(VerificationException::class));

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule2 ], $request, null)
			->once()
			->andReturn();

		$this->assertNull($this->handler->checkRule($rule, $request));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\VerificationException
	 * @expectedExceptionMessage None of the rules was met.
	 */
	public function testEitherException()
	{
		$rule = new Either();
		$rule1 = Mockery::mock(RuleInterface::class)
			->shouldReceive('getCode')
			->once()
			->andReturn(404)
			->getMock();
		$rule2 = Mockery::mock(RuleInterface::class);
		$rule->rules = [ $rule1, $rule2 ];
		$request = new Request('Test', 'GET', []);

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule1 ], $request, null)
			->once()
			->andThrow(Mockery::mock(VerificationException::class));

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule2 ], $request, null)
			->once()
			->andThrow(Mockery::mock(VerificationException::class));

		$this->handler->checkRule($rule, $request);
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 */
	public function testUnknownAnnotation()
	{
		$rule = Mockery::mock(RuleInterface::class);
		$request = new Request('Test', 'GET', []);

		$this->handler->checkRule($rule, $request);
	}

}
