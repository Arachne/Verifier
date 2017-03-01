<?php

namespace Tests\Functional;

use Arachne\Codeception\Module\NetteApplicationModule;
use Codeception\Test\Unit;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class LinksTest extends Unit
{
    /**
     * @var NetteApplicationModule
     */
    protected $tester;

    public function testLinkMacro()
    {
        $this->tester->amOnPage('/article/');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('Normal link');
        $this->tester->dontSee('Checked link');
        $this->tester->seeLink('Normal link', '/article/edit/1');
        $this->tester->dontSeeLink('Checked link', '/article/edit/1');
    }

    public function testRedirect()
    {
        $this->tester->amOnPage('/article/remove/1');
        $this->tester->seeResponseCodeIs(302);
        $this->tester->seeRedirectTo('/article/delete/1');
    }

    public function testRedirectCustomCode()
    {
        $this->tester->amOnPage('/article/redirect/1');
        $this->tester->seeResponseCodeIs(301);
        $this->tester->seeRedirectTo('/article/delete/1');
    }

    public function testRedirectNotAllowed()
    {
        $this->tester->amOnPage('/article/modify/1');
        // Response code should never be 302 because the redirect target action is not allowed.
        // It's actually 404 because there is no template.
        $this->tester->seeResponseCodeIs(404);
    }

    public function testActionNotAllowed()
    {
        $this->tester->amOnPage('/article/edit/1');
        $this->tester->seeResponseCodeIs(403);
    }
}
