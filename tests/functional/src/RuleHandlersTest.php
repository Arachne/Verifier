<?php

namespace Tests\Functional;

use Arachne\Verifier\Verifier;
use Codeception\TestCase\Test;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Tests\Functional\Classes\TestPresenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RuleHandlersTest extends Test
{
    /** @var Verifier */
    private $verifier;

    public function _before()
    {
        parent::_before();
        $this->verifier = $this->guy->grabService(Verifier::class);
    }

    public function testEitherFirst()
    {
        $request = new Request('Test', 'GET', [
            Presenter::ACTION_KEY => 'eitherfirst',
        ]);

        $this->assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testEitherSecond()
    {
        $request = new Request('Test', 'GET', [
            Presenter::ACTION_KEY => 'eithersecond',
        ]);

        $this->assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testEitherFalse()
    {
        $request = new Request('Test', 'GET', [
            Presenter::ACTION_KEY => 'eitherfalse',
        ]);

        $this->assertFalse($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testEitherInner()
    {
        $request = new Request('Test', 'GET', [
            Presenter::ACTION_KEY => 'eitherinner',
        ]);

        $this->assertFalse($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testAllTrue()
    {
        $request = new Request('Test', 'GET', [
            Presenter::ACTION_KEY => 'alltrue',
        ]);

        $this->assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testAllFalse()
    {
        $request = new Request('Test', 'GET', [
            Presenter::ACTION_KEY => 'allfalse',
        ]);

        $this->assertFalse($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testAllInner()
    {
        $request = new Request('Test', 'GET', [
            Presenter::ACTION_KEY => 'allinner',
        ]);

        $this->assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }
}
