<?php

namespace Tests\Unit;

use Arachne\Verifier\Annotations\AnnotationsRuleProvider;
use Codeception\MockeryModule\Test;
use Doctrine\Common\Annotations\AnnotationReader;
use Mockery;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;
use Tests\Unit\Classes\TestPresenter;
use Tests\Unit\Classes\TestRule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class AnnotationsRuleProviderTest extends Test
{

	/** @var AnnotationsRuleProvider */
	private $provider;

	protected function _before()
	{
		$reader = new AnnotationReader();
		$this->provider = new AnnotationsRuleProvider($reader);
	}

	public function testClassAnnotations()
	{
		$reflection = new ReflectionClass(TestPresenter::class);
		$this->assertEquals([ new TestRule() ], $this->provider->getRules($reflection));
	}

	public function testMethodAnnotations()
	{
		$reflection = new ReflectionMethod(TestPresenter::class, 'renderView');
		$this->assertEquals([ new TestRule(), new TestRule() ], $this->provider->getRules($reflection));
	}

	public function testPropertyAnnotations()
	{
		$reflection = new ReflectionProperty(TestPresenter::class, 'property');
		$this->assertEquals([ new TestRule() ], $this->provider->getRules($reflection));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either ReflectionMethod or ReflectionClass.
	 */
	public function testInvalidReflection()
	{
		$reflection = Mockery::mock(Reflector::class);
		$this->provider->getRules($reflection);
	}

}
