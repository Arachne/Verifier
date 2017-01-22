<?php

namespace Tests\Unit;

use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\Rules\All;
use Arachne\Verifier\Rules\AllRuleHandler;
use Arachne\Verifier\Verifier;
use Codeception\MockeryModule\Test;
use Mockery;
use Mockery\MockInterface;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class AllRuleHandlerTest extends Test
{
    /** @var AllRuleHandler */
    private $handler;

    /** @var MockInterface */
    private $verifier;

    protected function _before()
    {
        $this->verifier = Mockery::mock(Verifier::class);
        $this->handler = new AllRuleHandler($this->verifier);
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
            ->with($rule->rules, $request, null)
            ->once()
            ->andReturn();

        $this->assertNull($this->handler->checkRule($rule, $request));
    }

    /**
     * @expectedException \Arachne\Verifier\Exception\InvalidArgumentException
     */
    public function testUnknownAnnotation()
    {
        $rule = Mockery::mock(RuleInterface::class);
        $request = new Request('Test', 'GET', []);

        $this->handler->checkRule($rule, $request);
    }
}
