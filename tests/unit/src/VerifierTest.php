<?php

namespace Tests\Unit;

use Arachne\DIHelpers\ResolverInterface;
use Arachne\Verifier\Exception\UnexpectedTypeException;
use Arachne\Verifier\Exception\VerificationException;
use Arachne\Verifier\RuleHandlerInterface;
use Arachne\Verifier\RuleProviderInterface;
use Arachne\Verifier\Verifier;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\PresenterComponent;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;
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
     * @var InstanceHandle
     */
    private $handlerResolverHandle;

    /**
     * @var InstanceHandle
     */
    private $presenterFactoryHandle;

    /**
     * @var Verifier
     */
    private $verifier;

    protected function _before()
    {
        $this->ruleProviderHandle = Phony::mock(RuleProviderInterface::class);
        $this->handlerResolverHandle = Phony::mock(ResolverInterface::class);
        $this->presenterFactoryHandle = Phony::mock(IPresenterFactory::class);
        $this->verifier = new Verifier(
            $this->ruleProviderHandle->get(),
            $this->handlerResolverHandle->get(),
            $this->presenterFactoryHandle->get()
        );
    }

    public function testGetRulesOnClass()
    {
        $reflection = $this->createClassReflection();
        $this->setupRuleProviderMock();

        $this->assertEquals([new TestRule()], $this->verifier->getRules($reflection));
    }

    public function testGetRulesOnMethod()
    {
        $reflection = $this->createMethodReflection();
        $this->setupRuleProviderMock();

        $this->assertEquals([new TestRule()], $this->verifier->getRules($reflection));
    }

    /**
     * @expectedException \Arachne\Verifier\Exception\InvalidArgumentException
     * @expectedExceptionMessage Reflection must be an instance of either ReflectionMethod, ReflectionClass or ReflectionProperty.
     */
    public function testGetRulesOnReflector()
    {
        $reflection = Phony::mock(Reflector::class)->get();
        $this->verifier->getRules($reflection);
    }

    public function testCheckRules()
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupHandlerResolverMock($handler);

        $this->verifier->checkRules([new TestRule(), new TestRule()], $request);
    }

    public function testCheckReflectionOnClass()
    {
        $reflection = $this->createClassReflection();
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $this->verifier->checkReflection($reflection, $request);
        $this->verifier->checkReflection($reflection, $request);
    }

    public function testCheckReflectionOnMethod()
    {
        $reflection = $this->createMethodReflection();
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $this->verifier->checkReflection($reflection, $request);
        $this->verifier->checkReflection($reflection, $request);
    }

    public function testIsLinkVerifiedTrue()
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

        $this->assertTrue($this->verifier->isLinkVerified($request, Phony::mock(PresenterComponent::class)->get()));
    }

    public function testIsLinkVerifiedFalse()
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

        $this->assertFalse($this->verifier->isLinkVerified($request, Phony::mock(PresenterComponent::class)->get()));
    }

    public function testIsLinkVerifiedSignal()
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

        $this->assertTrue($this->verifier->isLinkVerified($request, $component));
    }

    public function testIsComponentVerifiedTrue()
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();
        $parent->setParent(null, 'Test');

        $this->assertTrue($this->verifier->isComponentVerified('component', $request, $parent));
    }

    public function testIsComponentVerifiedFalse()
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request, null, true);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();

        $this->assertFalse($this->verifier->isComponentVerified('component', $request, $parent));
    }

    public function testIsComponentSignalVerifiedTrue()
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

        $this->assertTrue($this->verifier->isLinkVerified($request, $component));
    }

    /**
     * @expectedException \Arachne\Verifier\Exception\InvalidArgumentException
     * @expectedExceptionMessage Wrong signal receiver, expected 'component' component but 'test-component' was given.
     */
    public function testWrongSignalReceiver()
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

    public function testInvalidRule()
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
            $this->fail();
        } catch (UnexpectedTypeException $e) {
        }

        $this->handlerResolverHandle
            ->resolve
            ->calledWith(InvalidRule::class);
    }

    public function testVerifyPropertiesTrue()
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();
        $parent->setParent(null, 'Test');

        $this->verifier->verifyProperties($request, $parent);
        $this->assertTrue($parent->property);
    }

    public function testVerifyPropertiesFalse()
    {
        $request = Phony::mock(Request::class)->get();
        $handler = $this->createHandlerMock($request, null, true);

        $this->setupRuleProviderMock();
        $this->setupHandlerResolverMock($handler);

        $parent = new TestPresenter();
        $parent->setParent(null, 'Test');

        $this->verifier->verifyProperties($request, $parent);
        $this->assertFalse($parent->property);
    }

    /**
     * @return ReflectionClass
     */
    private function createClassReflection()
    {
        $reflectionHandle = Phony::mock(ReflectionClass::class);
        $reflectionHandle
            ->getName
            ->returns('class');

        return $reflectionHandle->get();
    }

    /**
     * @return ReflectionMethod
     */
    private function createMethodReflection()
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

    /**
     * @param Request $request
     * @param string  $component
     * @param bool    $throw
     *
     * @return RuleHandlerInterface
     */
    private function createHandlerMock(Request $request, $component = null, $throw = false)
    {
        $ruleHandlerHandle = Phony::mock(RuleHandlerInterface::class);

        if ($throw) {
            $ruleHandlerHandle
                ->checkRule
                ->with(self::isInstanceOf(TestRule::class), $request, $component)
                ->throws(Phony::mock(VerificationException::class));
        }

        return $ruleHandlerHandle->get();
    }

    /**
     * @param array $parameters
     *
     * @return Request
     */
    private function createRequestMock(array $parameters)
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

    /**
     * @param RuleHandlerInterface $handler
     */
    private function setupHandlerResolverMock(RuleHandlerInterface $handler)
    {
        $this->handlerResolverHandle
            ->resolve
            ->with(TestRule::class)
            ->returns($handler);
    }

    private function setupRuleProviderMock()
    {
        $this->ruleProviderHandle
            ->getRules
            ->returns([new TestRule()]);
    }

    private function setupPresenterFactoryMock()
    {
        $this->presenterFactoryHandle
            ->getPresenterClass
            ->with('Test')
            ->returns(TestPresenter::class);
    }
}
