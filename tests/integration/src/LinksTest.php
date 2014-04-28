<?php

namespace Tests\Integration\Classes;

use Codeception\TestCase\Test;

class LinksTest extends Test
{

	public function testLinkMacro()
	{
		$this->codeGuy->amOnPage('/article/');
		$this->codeGuy->seeResponseCodeIs(200);
		$this->codeGuy->see('Normal link');
		$this->codeGuy->dontSee('Checked link');
		$this->codeGuy->seeLink('Normal link', '/article/edit/1');
		$this->codeGuy->dontSeeLink('Checked link', '/article/edit/1');
	}

	public function testRedirect()
	{
		$this->codeGuy->amOnPage('/article/remove/1');
		// Cannot check header Location for URL because it's not supported for CLI SAPI.
		$this->codeGuy->seeResponseCodeIs(302);
	}

	public function testRedirectCustomCode()
	{
		$this->codeGuy->amOnPage('/article/redirect/1');
		// Cannot check header Location for URL because it's not supported for CLI SAPI.
		$this->codeGuy->seeResponseCodeIs(301);
	}

	public function testRedirectNotAllowed()
	{
		$this->codeGuy->amOnPage('/article/modify/1');
		// Response code should never be 302 because the redirect target action is not allowed.
		// It's actually 404 because there is no template.
		$this->codeGuy->seeResponseCodeIs(404);
	}

	public function testActionNotAllowed()
	{
		$this->codeGuy->amOnPage('/article/edit/1');
		$this->codeGuy->seeResponseCodeIs(403);
	}

}
