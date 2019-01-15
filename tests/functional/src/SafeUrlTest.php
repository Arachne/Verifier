<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Test\Unit;
use Contributte\Codeception\Module\NetteApplicationModule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class SafeUrlTest extends Unit
{
    /**
     * @var NetteApplicationModule
     */
    protected $tester;

    public function testHrefMacro(): void
    {
        $this->tester->amOnPage('/article/safeurl');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->debugContent();
        $this->tester->seeLink('Safe url', '');
        $this->tester->dontSeeLink('Safe url', 'javascript');
    }
}
