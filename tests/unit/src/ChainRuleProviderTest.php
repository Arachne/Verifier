<?php

namespace Tests\Unit;

use Arachne\Verifier\ChainRuleProvider;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\RuleProviderInterface;
use ArrayIterator;
use Codeception\MockeryModule\Test;
use Mockery;
use Reflector;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ChainRuleProviderTest extends Test
{
    protected function _before()
    {
        $this->ruleProvider1 = Mockery::mock(RuleProviderInterface::class);
        $this->ruleProvider2 = Mockery::mock(RuleProviderInterface::class);
        $this->chainRuleProvider = new ChainRuleProvider(new ArrayIterator([$this->ruleProvider1, $this->ruleProvider2]));
    }

    public function testProviderReturningNull()
    {
        $reflector = Mockery::mock(Reflector::class);

        $this->ruleProvider1
            ->shouldReceive('getRules')
            ->with($reflector)
            ->once();

        $this->ruleProvider2
            ->shouldReceive('getRules')
            ->with($reflector)
            ->once();

        $this->assertSame([], $this->chainRuleProvider->getRules($reflector));
    }

    public function testMergingProviderResults()
    {
        $reflector = Mockery::mock(Reflector::class);

        $rule1 = Mockery::mock(RuleInterface::class);
        $this->ruleProvider1
            ->shouldReceive('getRules')
            ->with($reflector)
            ->once()
            ->andReturn([$rule1]);

        $rule2 = Mockery::mock(RuleInterface::class);
        $this->ruleProvider2
            ->shouldReceive('getRules')
            ->with($reflector)
            ->once()
            ->andReturn([$rule2]);

        $this->assertSame([$rule1, $rule2], $this->chainRuleProvider->getRules($reflector));
    }
}
