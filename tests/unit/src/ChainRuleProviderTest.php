<?php

namespace Tests\Unit;

use Arachne\Verifier\ChainRuleProvider;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\RuleProviderInterface;
use ArrayIterator;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;
use Reflector;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ChainRuleProviderTest extends Unit
{
    /**
     * @var InstanceHandle
     */
    private $ruleProvider1Handle;

    /**
     * @var InstanceHandle
     */
    private $ruleProvider2Handle;

    /**
     * @var ChainRuleProvider
     */
    private $chainRuleProvider;

    protected function _before()
    {
        $this->ruleProvider1Handle = Phony::mock(RuleProviderInterface::class);
        $this->ruleProvider2Handle = Phony::mock(RuleProviderInterface::class);
        $this->chainRuleProvider = new ChainRuleProvider(new ArrayIterator([$this->ruleProvider1Handle->get(), $this->ruleProvider2Handle->get()]));
    }

    public function testProviderReturningNull()
    {
        $reflector = Phony::mock(Reflector::class)->get();

        self::assertSame([], $this->chainRuleProvider->getRules($reflector));

        $this->ruleProvider1Handle
            ->getRules
            ->calledWith($reflector);

        $this->ruleProvider2Handle
            ->getRules
            ->calledWith($reflector);
    }

    public function testMergingProviderResults()
    {
        $reflector = Phony::mock(Reflector::class)->get();

        $rule1 = Phony::mock(RuleInterface::class)->get();

        $this->ruleProvider1Handle
            ->getRules
            ->with($reflector)
            ->returns([$rule1]);

        $rule2 = Phony::mock(RuleInterface::class)->get();

        $this->ruleProvider2Handle
            ->getRules
            ->with($reflector)
            ->returns([$rule2]);

        self::assertSame([$rule1, $rule2], $this->chainRuleProvider->getRules($reflector));
    }
}
