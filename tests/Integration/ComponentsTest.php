<?php

namespace Arachne\Tests\Verifier\Integration\Classes;

use Codeception\TestCase\Test;

class ComponentsTest extends Test
{

	public function testComponentMacro()
	{
		$this->codeGuy->amOnPage('/article/');
		$this->codeGuy->seeResponseCodeIs(200);
		$this->codeGuy->see('header');
		$this->codeGuy->dontSee('footer');
	}

	public function testLinkMacroInComponent()
	{
		$this->codeGuy->amOnPage('/article/');
		$this->codeGuy->seeResponseCodeIs(200);
		$this->codeGuy->see('Component link');
		$this->codeGuy->seeLink('Component link', '/article/');
		$this->codeGuy->see('Component signal link true');
		$this->codeGuy->seeLink('Component signal link true', '/article/?header-parameter=1&do=header-signal');
		$this->codeGuy->dontSee('Component signal link false');
		$this->codeGuy->dontSee('Signal called!');
	}

	public function testComponentSignal()
	{
		$this->codeGuy->amOnPage('/article/?do=header-signal&header-parameter=1');
		$this->codeGuy->seeResponseCodeIs(200);
		$this->codeGuy->see('Signal called!');
	}

	public function testComponentNotAllowed()
	{
		$this->codeGuy->amOnPage('/article/component-not-enabled');
		$this->codeGuy->seeResponseCodeIs(403);
	}

}
