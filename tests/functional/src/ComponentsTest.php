<?php

namespace Tests\Functional;

use Codeception\TestCase\Test;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ComponentsTest extends Test
{

	public function testComponentMacro()
	{
		$this->guy->amOnPage('/article/');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->see('header');
		$this->guy->dontSee('footer');
		$this->guy->see('fallback');
	}

	public function testLinkMacroInComponent()
	{
		$this->guy->amOnPage('/article/');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->see('Component link');
		$this->guy->seeLink('Component link', '/article/');
		$this->guy->see('Component signal link true');
		$this->guy->seeLink('Component signal link true', '/article/?header-parameter=1&do=header-signal');
		$this->guy->dontSee('Component signal link false');
		$this->guy->dontSee('Signal called!');
	}

	public function testComponentSignal()
	{
		$this->guy->amOnPage('/article/?do=header-signal&header-parameter=1');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->see('Signal called!');
	}

	public function testComponentNotAllowed()
	{
		$this->guy->amOnPage('/article/component-not-enabled');
		$this->guy->seeResponseCodeIs(403);
	}

	public function testSubComponentAllowed()
	{
		$this->guy->amOnPage('/article/?do=parent-child-signal1&parent-child-parameter=1');
		$this->guy->seeResponseCodeIs(302);
		$this->guy->seeRedirectTo('/article/?do=parent-child-signal2');
	}

	public function testSubComponentNotAllowed()
	{
		$this->guy->amOnPage('/article/?do=parent-child-signal1&parent-child-parameter=0');
		$this->guy->seeResponseCodeIs(403);
	}

}
