<?php

declare(strict_types=1);

namespace Tests\Functional;

use Arachne\Codeception\Module\NetteDIModule;
use Arachne\Verifier\Verifier;
use Codeception\Test\Unit;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Tests\Functional\Classes\TestPresenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RuleHandlersTest extends Unit
{
    /**
     * @var NetteDIModule
     */
    protected $tester;

    /**
     * @var Verifier
     */
    private $verifier;

    public function _before(): void
    {
        $this->verifier = $this->tester->grabService(Verifier::class);
    }

    public function testEitherFirst(): void
    {
        $request = new Request(
            'Test',
            'GET',
            [
                Presenter::ACTION_KEY => 'eitherfirst',
            ]
        );

        self::assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testEitherSecond(): void
    {
        $request = new Request(
            'Test',
            'GET',
            [
                Presenter::ACTION_KEY => 'eithersecond',
            ]
        );

        self::assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testEitherFalse(): void
    {
        $request = new Request(
            'Test',
            'GET',
            [
                Presenter::ACTION_KEY => 'eitherfalse',
            ]
        );

        self::assertFalse($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testEitherInner(): void
    {
        $request = new Request(
            'Test',
            'GET',
            [
                Presenter::ACTION_KEY => 'eitherinner',
            ]
        );

        self::assertFalse($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testAllTrue(): void
    {
        $request = new Request(
            'Test',
            'GET',
            [
                Presenter::ACTION_KEY => 'alltrue',
            ]
        );

        self::assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testAllFalse(): void
    {
        $request = new Request(
            'Test',
            'GET',
            [
                Presenter::ACTION_KEY => 'allfalse',
            ]
        );

        self::assertFalse($this->verifier->isLinkVerified($request, new TestPresenter()));
    }

    public function testAllInner(): void
    {
        $request = new Request(
            'Test',
            'GET',
            [
                Presenter::ACTION_KEY => 'allinner',
            ]
        );

        self::assertTrue($this->verifier->isLinkVerified($request, new TestPresenter()));
    }
}
