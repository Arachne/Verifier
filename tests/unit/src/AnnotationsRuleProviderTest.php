<?php

namespace Tests\Unit;

use Arachne\Verifier\AnnotationsRuleProvider;
use Codeception\TestCase\Test;
use Doctrine\Common\Annotations\AnnotationReader;
use Mockery;
use ReflectionClass;
use ReflectionMethod;
use Tests\Unit\Classes\TestRule;

/**
 * @author Jáchym Toušek
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
		$reflection = new ReflectionClass('Tests\Unit\Classes\TestPresenter');
		$request = Mockery::mock('Nette\Application\Request');
		$this->assertEquals([ new TestRule() ], $this->provider->getRules($reflection, $request));
	}

	public function testMethodAnnotations()
	{
		$reflection = new ReflectionMethod('Tests\Unit\Classes\TestPresenter', 'renderView');
		$request = Mockery::mock('Nette\Application\Request');
		$this->assertEquals([ new TestRule(), new TestRule() ], $this->provider->getRules($reflection, $request));
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\InvalidArgumentException
	 * @expectedExceptionMessage Reflection must be an instance of either ReflectionMethod or ReflectionClass.
	 */
	public function testInvalidReflection()
	{
		$reflection = Mockery::mock('Reflector');
		$request = Mockery::mock('Nette\Application\Request');
		$this->provider->getRules($reflection, $request);
	}

}
