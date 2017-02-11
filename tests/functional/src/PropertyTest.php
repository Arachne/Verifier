<?php

namespace Tests\Functional;

use Codeception\Test\Unit;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class PropertyTest extends Unit
{
    protected $tester;

    public function testNoProperty()
    {
        $this->tester->amOnPage('/article/');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->dontSee('Presenter property verified');
        $this->tester->dontSee('Component property verified');
    }

    public function testPresenterProperty()
    {
        $this->tester->amOnPage('/article/?privilege=1');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('Presenter property verified');
        $this->tester->dontSee('Component property verified');
    }

    public function testComponentProperty()
    {
        $this->tester->amOnPage('/article/?header-privilege=1');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->dontSee('Presenter property verified');
        $this->tester->see('Component property verified');
    }
}
