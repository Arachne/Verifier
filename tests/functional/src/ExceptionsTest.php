<?php

namespace Tests\Functional;

use Arachne\Codeception\Module\NetteDIModule;
use Arachne\Verifier\Exception\NotSupportedException;
use Codeception\Test\Unit;
use Nette\Application\BadRequestException;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ExceptionsTest extends Unit
{
    /**
     * @var NetteDIModule
     */
    protected $tester;

    public function testRenderMethod()
    {
        $request = new Request(
            'Article',
            'GET',
            [
                Presenter::ACTION_KEY => 'view',
            ]
        );

        /** @var Presenter $presenter */
        $presenter = $this->tester
            ->grabService(IPresenterFactory::class)
            ->createPresenter('Article');

        // Canonicalization is broken in CLI.
        $presenter->autoCanonicalize = false;

        try {
            $presenter->run($request);
            self::fail();
        } catch (NotSupportedException $e) {
            self::assertSame('Rules for render methods are not supported. Define the rules for action method instead.', $e->getMessage());
        }
    }

    public function testUndefinedAction()
    {
        $request = new Request(
            'Article',
            'GET',
            [
                Presenter::ACTION_KEY => 'UndefinedAction',
            ]
        );

        /** @var Presenter $presenter */
        $presenter = $this->tester
            ->grabService(IPresenterFactory::class)
            ->createPresenter('Article');

        try {
            $presenter->run($request);
            self::fail();
        } catch (BadRequestException $e) {
            self::assertSame(404, $e->getCode());
            self::assertSame('Action \'Tests\Functional\Classes\ArticlePresenter::actionUndefinedAction\' does not exist.', $e->getMessage());
        }
    }
}
