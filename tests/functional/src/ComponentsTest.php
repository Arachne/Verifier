<?php

namespace Tests\Functional;

use Codeception\Test\Unit;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ComponentsTest extends Unit
{
    protected $tester;

    public function testComponentMacro()
    {
        $this->tester->amOnPage('/article/');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('header');
        $this->tester->dontSee('footer');
        $this->tester->see('fallback');
    }

    public function testLinkMacroInComponent()
    {
        $this->tester->amOnPage('/article/');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('Component link');
        $this->tester->seeLink('Component link', '/article/');
        $this->tester->see('Component signal link true');
        $this->tester->seeLink('Component signal link true', '/article/?header-parameter=1&do=header-signal');
        $this->tester->dontSee('Component signal link false');
        $this->tester->dontSee('Signal called!');
    }

    public function testComponentSignal()
    {
        $this->tester->amOnPage('/article/?do=header-signal&header-parameter=1');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('Signal called!');
    }

    public function testComponentNotAllowed()
    {
        $this->tester->amOnPage('/article/component-not-enabled');
        $this->tester->seeResponseCodeIs(403);
    }

    public function testSubComponentAllowed()
    {
        $this->tester->amOnPage('/article/?do=parent-child-signal1&parent-child-parameter=1');
        $this->tester->seeResponseCodeIs(302);
        $this->tester->seeRedirectTo('/article/?do=parent-child-signal2');
    }

    public function testSubComponentNotAllowed()
    {
        $this->tester->amOnPage('/article/?do=parent-child-signal1&parent-child-parameter=0');
        $this->tester->seeResponseCodeIs(403);
    }
}
