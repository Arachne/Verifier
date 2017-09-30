<?php

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @TestRule
 */
class TestPresenter extends Presenter
{
    /**
     * @TestRule
     */
    public $property;

    /**
     * @TestRule
     */
    public function actionAction(): void
    {
    }

    /**
     * This should be ignored.
     *
     * @TestRule
     */
    public function renderAction(): void
    {
    }

    /**
     * @TestRule
     * @TestRule
     */
    public function renderView(): void
    {
    }

    /**
     * @TestRule
     */
    public function handleSignal(): void
    {
    }

    /**
     * @TestRule
     */
    public function createComponentComponent(): void
    {
    }
}
