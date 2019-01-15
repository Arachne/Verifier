<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Test\Unit;
use Contributte\Codeception\Module\NetteApplicationModule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class PropertyTest extends Unit
{
    /**
     * @var NetteApplicationModule
     */
    protected $tester;

    public function testNoProperty(): void
    {
        $this->tester->amOnPage('/article/');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->dontSee('Presenter property verified');
        $this->tester->dontSee('Component property verified');
    }

    public function testPresenterProperty(): void
    {
        $this->tester->amOnPage('/article/?privilege=1');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->see('Presenter property verified');
        $this->tester->dontSee('Component property verified');
    }

    public function testComponentProperty(): void
    {
        $this->tester->amOnPage('/article/?header-privilege=1');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->dontSee('Presenter property verified');
        $this->tester->see('Component property verified');
    }
}
