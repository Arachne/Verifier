<?php

declare(strict_types=1);

namespace Tests\Unit;

use Arachne\Verifier\Exception\UnexpectedTypeException;
use Arachne\Verifier\Exception\VerificationException;
use Arachne\Verifier\RuleHandlerInterface;
use Arachne\Verifier\RuleProviderInterface;
use Arachne\Verifier\Verifier;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;
use Eloquent\Phony\Stub\StubVerifier;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\PresenterComponent;
use ReflectionClass;
use ReflectionMethod;
use Tests\Unit\Classes\InvalidRule;
use Tests\Unit\Classes\TestControl;
use Tests\Unit\Classes\TestPresenter;
use Tests\Unit\Classes\TestRule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class VerifierTest extends Unit
{
    /**
     * @var InstanceHandle
     */
    private $ruleProviderHandle;

    /**
     * @var StubVerifier
     */
    private $handlerResolver;

    /**
     * @var InstanceHandle
     */
    private $presenterFactoryHandle;

    /**
     * @var Verifier
     */
    private $verifier;

    protected function _before(): void
    {
        $this->ruleProviderHandle = Phony::mock(RuleProviderInterface::class);
        $this->handlerResolver = Phony::stub();
        $this->presenterFactoryHandle = Phony::mock(IPresenterFactory::class);
        $this->verifier = new Verifier(
            $this->ruleProviderHandle->get(),
            $this->handlerResolver,
            $this->presenterFactoryHandle->get()
        );
    }

    public function testGetRulesOnClass(): void
    {
        $reflection = $this->createClassReflection();
        $this->setupRuleProviderMock();

        self::assertEquals([new TestRule()], $this->verifier->getRules($reflection));
    }

    public function testGetRulesOnMethod(): void
    {
        $reflection = $this->createMethodReflection();
        $this->setupRuleProviderMock();

        self::assertEquals([new TestRule()], $this->verifier->getRules($reflection));
    }

    public function testCheckRules(): void
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupHandlerResolverMock($handler);

        $this->verifier->checkRules([new TestRule(), new TestRule()], $request);
    }

    public function testCheckReflectionOnClass(): void
    {
        $reflection = $this->createClassReflection();
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $this->verifier->checkReflection($reflection, $request);
        $this->verifier->checkReflection($reflection, $request);
    }

    public function testCheckReflectionOnMethod(): void
    {
        $reflection = $this->createMethodReflection();
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $this->verifier->checkReflection($reflection, $request);
        $this->verifier->checkReflection($reflection, $request);
    }

    public function testIsLinkVerifiedTrue(): void
    {
        $request = $this->createRequestMock(
            [
                Presenter::ACTION_KEY => 'action',
            ]
        );
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);
        $this->setupPresenterFactoryMock();

        self::assertTrue($this->verifier->isLinkVerified($request, Phony::mock(PresenterComponent::class)->get()));
    }

    public function testIsLinkVerifiedFalse(): void
    {
        $request = $this->createRequestMock(
            [
                Presenter::ACTION_KEY => 'view',
            ]
        );
        $handler = $this->createHandlerMock($request, null, true);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);
        $this->setupPresenterFactoryMock();

        self::assertFalse($this->verifier->isLinkVerified($request, Phony::mock(PresenterComponent::class)->get()));
    }

    public function testIsLinkVerifiedSignal(): void
    {
        $request = $this->createRequestMock(
            [
                Presenter::ACTION_KEY => 'action',
                Presenter::SIGNAL_KEY => 'signal',
            ]
        );
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);
        $this->setupPresenterFactoryMock();

        $component = Phony::mock(PresenterComponent::class)->get();

        self::assertTrue($this->verifier->isLinkVerified($request, $component));
    }

    public function testIsComponentVerifiedTrue(): void
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();
        $parent->setParent(null, 'Test');

        self::assertTrue($this->verifier->isComponentVerified('component', $request, $parent));
    }

    public function testIsComponentVerifiedFalse(): void
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request, null, true);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();

        self::assertFalse($this->verifier->isComponentVerified('component', $request, $parent));
    }

    public function testIsComponentSignalVerifiedTrue(): void
    {
        $request = $this->createRequestMock(
            [
                Presenter::ACTION_KEY => 'action',
                Presenter::SIGNAL_KEY => 'component-signal',
            ]
        );
        $handler = $this->createHandlerMock($request, 'component');

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $component = new TestControl(null, 'component');
        $parent = Phony::partialMock(Presenter::class)->get();
        $component->setParent($parent);

        self::assertTrue($this->verifier->isLinkVerified($request, $component));
    }

    /**
     * @expectedException \Arachne\Verifier\Exception\InvalidArgumentException
     * @expectedExceptionMessage Wrong signal receiver, expected 'component' component but 'test-component' was given.
     */
    public function testWrongSignalReceiver(): void
    {
        $request = $this->createRequestMock(
            [
                Presenter::ACTION_KEY => 'action',
                Presenter::SIGNAL_KEY => 'component-signal',
            ]
        );

        $componentHandle = Phony::mock(PresenterComponent::class);
        $componentHandle
            ->getUniqueId
            ->returns('test-component');

        $this->verifier->isLinkVerified($request, $componentHandle->get());
    }

    public function testInvalidRule(): void
    {
        $request = $this->createRequestMock(
            [
                Presenter::ACTION_KEY => 'invalid',
            ]
        );

        $this->setupPresenterFactoryMock();
        $this->ruleProviderHandle
            ->getRules
            ->returns([new InvalidRule()]);

        $component = Phony::mock(PresenterComponent::class)->get();

        try {
            $this->verifier->isLinkVerified($request, $component);
            self::fail();
        } catch (UnexpectedTypeException $e) {
        }

        $this->handlerResolver
            ->calledWith(InvalidRule::class);
    }

    public function testVerifyPropertiesTrue(): void
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();
        $parent->setParent(null, 'Test');

        $this->verifier->verifyProperties($request, $parent);
        self::assertTrue($parent->property);
    }

    public function testVerifyPropertiesFalse(): void
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request, null, true);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();
        $parent->setParent(null, 'Test');

        $this->verifier->verifyProperties($request, $parent);
        self::assertFalse($parent->property);
    }

    private function createClassReflection(): ReflectionClass
    {
        $reflectionHandle = Phony::mock(ReflectionClass::class);
        $reflectionHandle
            ->getName
            ->returns('class');

        return $reflectionHandle->get();
    }

    private function createMethodReflection(): ReflectionMethod
    {
        $methodHandle = Phony::mock(ReflectionMethod::class);
        $methodHandle
            ->getName
            ->returns('method');
        $methodHandle
            ->getDeclaringClass
            ->returns($this->createClassReflection());

        return $methodHandle->get();
    }

    private function createHandlerMock(Request $request, ?string $component = null, bool $throw = false): RuleHandlerInterface
    {
        $ruleHandlerHandle = Phony::mock(RuleHandlerInterface::class);

        if ($throw) {
            $ruleHandlerHandle
                ->checkRule
                ->with(self::isInstanceOf(TestRule::class), $request, $component)
                ->throws(Phony::mock(VerificationException::class)->get());
        }

        return $ruleHandlerHandle->get();
    }

    private function createRequestMock(array $parameters): Request
    {
        $requestHandle = Phony::mock(Request::class);
        $requestHandle
            ->getParameters
            ->returns($parameters);

        $requestHandle
            ->getPresenterName
            ->returns('Test');

        return $requestHandle->get();
    }

    private function setupHandlerResolverMock(RuleHandlerInterface $handler): void
    {
        $this->handlerResolver
            ->with(TestRule::class)
            ->returns($handler);
    }

    private function setupRuleProviderMock(): void
    {
        $this->ruleProviderHandle
            ->getRules
            ->returns([new TestRule()]);
    }

    private function setupPresenterFactoryMock(): void
    {
        $this->presenterFactoryHandle
            ->getPresenterClass
            ->with('Test')
            ->returns(TestPresenter::class);
    }
}
