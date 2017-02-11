<?php

namespace Tests\Functional;

use Codeception\Test\Unit;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ExceptionsTest extends Unit
{
    protected $tester;

    /**
     * @expectedException \Arachne\Verifier\Exception\NotSupportedException
     * @expectedExceptionMessage Rules for render methods are not supported. Define the rules for action method instead.
     */
    public function testRenderMethod()
    {
        $request = new Request(
            'Article',
            'GET',
            [
                Presenter::ACTION_KEY => 'view',
            ]
        );

        $presenter = $this->tester
            ->grabService(IPresenterFactory::class)
            ->createPresenter('Article');
        // Canonicalization is broken in CLI.
        $presenter->autoCanonicalize = false;
        $presenter->run($request);
    }

    /**
     * @expectedException \Nette\Application\BadRequestException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Action 'Tests\Functional\Classes\ArticlePresenter::actionUndefinedAction' does not exist.
     */
    public function testUndefinedAction()
    {
        $request = new Request(
            'Article',
            'GET',
            [
                Presenter::ACTION_KEY => 'UndefinedAction',
            ]
        );
        $this->tester
            ->grabService(IPresenterFactory::class)
            ->createPresenter('Article')
            ->run($request);
    }
}
