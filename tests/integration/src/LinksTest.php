<?php

namespace Tests\Integration;

use Codeception\TestCase\Test;

/**
 * @author Jáchym Toušek
 */
class LinksTest extends Test
{

	public function testLinkMacro()
	{
		$this->guy->amOnPage('/article/');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->see('Normal link');
		$this->guy->dontSee('Checked link');
		$this->guy->seeLink('Normal link', '/article/edit/1');
		$this->guy->dontSeeLink('Checked link', '/article/edit/1');
	}

	public function testRedirect()
	{
		$this->guy->amOnPage('/article/remove/1');
		// Cannot check header Location for URL because it's not supported for CLI SAPI.
		$this->guy->seeResponseCodeIs(302);
	}

	public function testRedirectCustomCode()
	{
		$this->guy->amOnPage('/article/redirect/1');
		// Cannot check header Location for URL because it's not supported for CLI SAPI.
		$this->guy->seeResponseCodeIs(301);
	}

	public function testRedirectNotAllowed()
	{
		$this->guy->amOnPage('/article/modify/1');
		// Response code should never be 302 because the redirect target action is not allowed.
		// It's actually 404 because there is no template.
		$this->guy->seeResponseCodeIs(404);
	}

	public function testActionNotAllowed()
	{
		$this->guy->amOnPage('/article/edit/1');
		$this->guy->seeResponseCodeIs(403);
	}

	/**
	 * @expectedException Arachne\Verifier\Exception\NotSupportedException
	 * @ExpectedExceptionMessage Rules for render methods are not supported. Define the rules for action method instead.	 
	 */
	public function testRenderMethod()
	{
		$this->guy->amOnPage('/article/view');
	}

}
