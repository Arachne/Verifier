<?php

namespace Tests\Integration;

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

	public function testComponentNotAllowed()
	{
		$this->codeGuy->amOnPage('/article/component-not-enabled');
		$this->codeGuy->seeResponseCodeIs(403);
	}

}
