<?php

declare(strict_types=1);

namespace Tests\Unit;

use Arachne\Verifier\Exception\VerificationException;
use Arachne\Verifier\RuleInterface;
use Arachne\Verifier\Rules\Either;
use Arachne\Verifier\Rules\EitherRuleHandler;
use Arachne\Verifier\Verifier;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;
use Nette\Application\Request;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class EitherRuleHandlerTest extends Unit
{
    /**
     * @var InstanceHandle
     */
    private $verifierHandle;

    /**
     * @var EitherRuleHandler
     */
    private $handler;

    protected function _before(): void
    {
        $this->verifierHandle = Phony::mock(Verifier::class);
        $this->handler = new EitherRuleHandler($this->verifierHandle->get());
    }

    public function testEitherFirst(): void
    {
        $rule = new Either();
        $rule->rules = [
            $rule1 = Phony::mock(RuleInterface::class)->get(),
            $rule2 = Phony::mock(RuleInterface::class)->get(),
        ];
        $request = new Request('Test', 'GET', []);

        $this->handler->checkRule($rule, $request);

        $this->verifierHandle
            ->checkRules
            ->calledWith([$rule1], $request, null);
    }

    public function testEitherSecond(): void
    {
        $rule = new Either();
        $rule->rules = [
            $rule1 = Phony::mock(RuleInterface::class)->get(),
            $rule2 = Phony::mock(RuleInterface::class)->get(),
        ];
        $request = new Request('Test', 'GET', []);

        $this->verifierHandle
            ->checkRules
            ->with([$rule1], $request, null)
            ->throws(Phony::mock(VerificationException::class)->get());

        $this->handler->checkRule($rule, $request);
    }

    public function testEitherException(): void
    {
        $rule = new Either();
        $rule1Handle = Phony::mock(RuleInterface::class);
        $rule1Handle
            ->getCode
            ->returns(404);

        $rule2Handle = Phony::mock(RuleInterface::class);

        $rule1 = $rule1Handle->get();
        $rule2 = $rule2Handle->get();

        $rule->rules = [$rule1, $rule2];
        $request = new Request('Test', 'GET', []);

        $this->verifierHandle
            ->checkRules
            ->with([$rule1], $request, null)
            ->throws(Phony::mock(VerificationException::class)->get());

        $this->verifierHandle
            ->checkRules
            ->with([$rule2], $request, null)
            ->throws(Phony::mock(VerificationException::class)->get());

        try {
            $this->handler->checkRule($rule, $request);
            self::fail();
        } catch (VerificationException $e) {
            self::assertSame('None of the rules was met.', $e->getMessage());
        }
    }
}
