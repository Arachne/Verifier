<?php

namespace Tests\Unit;

use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\Rules\All;
use Arachne\Verifier\Rules\Either;
use Arachne\Verifier\Rules\CascadeRuleHandler;
use Arachne\Verifier\Verifier;
use Codeception\TestCase\Test;
use Mockery;
use Mockery\MockInterface;
use Nette\Application\BadRequestException;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class CascadeRuleHandlerTest extends Test
{

	/** @var CascadeRuleHandler */
	private $handler;

	/** @var MockInterface */
	private $verifier;

	protected function _before()
	{
		$this->verifier = Mockery::mock(Verifier::class);
		$this->handler = new CascadeRuleHandler($this->verifier);
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
			->with([ $rule1 ], $request, NULL)
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
			->with([ $rule1 ], $request, NULL)
			->once()
			->andThrow(Mockery::mock(BadRequestException::class));

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule2 ], $request, NULL)
			->once()
			->andReturn();

		$this->assertNull($this->handler->checkRule($rule, $request));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\FailedEitherVerificationException
	 * @expectedExceptionMessage None of the rules was met.
	 */
	public function testEitherException()
	{
		$rule = new Either();
		$rule->rules = [
			$rule1 = Mockery::mock(RuleInterface::class),
			$rule2 = Mockery::mock(RuleInterface::class),
		];
		$request = new Request('Test', 'GET', []);

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule1 ], $request, NULL)
			->once()
			->andThrow(Mockery::mock(BadRequestException::class));

		$this->verifier
			->shouldReceive('checkRules')
			->with([ $rule2 ], $request, NULL)
			->once()
			->andThrow(Mockery::mock(BadRequestException::class));

		$this->handler->checkRule($rule, $request);
	}

	public function testAll()
	{
		$rule = new All();
		$rule->rules = [
			Mockery::mock(RuleInterface::class),
			Mockery::mock(RuleInterface::class),
		];
		$request = new Request('Test', 'GET', []);

		$this->verifier
			->shouldReceive('checkRules')
			->with($rule->rules, $request, NULL)
			->once()
			->andReturn();

		$this->assertNull($this->handler->checkRule($rule, $request));
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
