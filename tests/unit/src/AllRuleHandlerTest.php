<?php

namespace Tests\Unit;

use Arachne\Verifier\Exception\InvalidArgumentException;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\Rules\All;
use Arachne\Verifier\Rules\AllRuleHandler;
use Arachne\Verifier\Verifier;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class AllRuleHandlerTest extends Unit
{
    /**
     * @var AllRuleHandler
     */
    private $handler;

    /**
     * @var InstanceHandle
     */
    private $verifierHandle;

    protected function _before()
    {
        $this->verifierHandle = Phony::mock(Verifier::class);
        $this->handler = new AllRuleHandler($this->verifierHandle->get());
    }

    public function testAll()
    {
        $rule = new All();
        $rule->rules = [
            Phony::mock(RuleInterface::class)->get(),
            Phony::mock(RuleInterface::class)->get(),
        ];
        $request = new Request('Test', 'GET', []);

        $this->handler->checkRule($rule, $request);

        $this->verifierHandle
            ->checkRules
            ->calledWith($rule->rules, $request, null);
    }

    public function testUnknownAnnotation()
    {
        $rule = Phony::mock(RuleInterface::class)->get();
        $request = new Request('Test', 'GET', []);

        try {
            $this->handler->checkRule($rule, $request);
            self::fail();
        } catch (InvalidArgumentException $e) {
        }
    }
}
