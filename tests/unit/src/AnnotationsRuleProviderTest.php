<?php

declare(strict_types=1);

namespace Tests\Unit;

use Arachne\Verifier\Annotations\AnnotationsRuleProvider;
use Arachne\Verifier\Exception\InvalidArgumentException;
use Codeception\Test\Unit;
use Doctrine\Common\Annotations\AnnotationReader;
use Eloquent\Phony\Phpunit\Phony;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;
use Tests\Unit\Classes\TestPresenter;
use Tests\Unit\Classes\TestRule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class AnnotationsRuleProviderTest extends Unit
{
    /**
     * @var AnnotationsRuleProvider
     */
    private $provider;

    protected function _before(): void
    {
        $reader = new AnnotationReader();
        $this->provider = new AnnotationsRuleProvider($reader);
    }

    public function testClassAnnotations(): void
    {
        $reflection = new ReflectionClass(TestPresenter::class);
        self::assertEquals([new TestRule()], $this->provider->getRules($reflection));
    }

    public function testMethodAnnotations(): void
    {
        $reflection = new ReflectionMethod(TestPresenter::class, 'renderView');
        self::assertEquals([new TestRule(), new TestRule()], $this->provider->getRules($reflection));
    }

    public function testPropertyAnnotations(): void
    {
        $reflection = new ReflectionProperty(TestPresenter::class, 'property');
        self::assertEquals([new TestRule()], $this->provider->getRules($reflection));
    }

    public function testInvalidReflection(): void
    {
        $reflection = Phony::mock(Reflector::class)->get();
        try {
            $this->provider->getRules($reflection);
            self::fail();
        } catch (InvalidArgumentException $e) {
            self::assertSame('Reflection must be an instance of either ReflectionMethod, ReflectionClass or ReflectionProperty.', $e->getMessage());
        }
    }
}
