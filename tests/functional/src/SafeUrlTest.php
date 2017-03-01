<?php

namespace Tests\Functional;

use Arachne\Codeception\Module\NetteApplicationModule;
use Codeception\Test\Unit;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class SafeUrlTest extends Unit
{
    /**
     * @var NetteApplicationModule
     */
    protected $tester;

    public function testHrefMacro()
    {
        $this->tester->amOnPage('/article/safeurl');
        $this->tester->seeResponseCodeIs(200);
        $this->tester->debugContent();
        $this->tester->seeLink('Safe url', '');
        $this->tester->dontSeeLink('Safe url', 'javascript');
    }
}
