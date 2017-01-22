<?php

namespace Tests\Functional\Classes;

use Arachne\Verifier\Rules\All;
use Arachne\Verifier\Rules\Either;
use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class TestPresenter extends Presenter
{
    /**
     * @Either(@Enabled(TRUE))
     */
    public function actionEitherFirst()
    {
    }

    /**
     * @Either({
     *   @Enabled(FALSE),
     *   @Enabled(TRUE),
     * })
     */
    public function actionEitherSecond()
    {
    }

    /**
     * @Either({
     *   @Enabled(FALSE),
     *   @Enabled(FALSE),
     * })
     */
    public function actionEitherFalse()
    {
    }

    /**
     * @Either({
     *   @Either
     * })
     */
    public function actionEitherInner()
    {
    }

    /**
     * @All(@Enabled(TRUE))
     */
    public function actionAllTrue()
    {
    }

    /**
     * @All({
     *   @Enabled(FALSE),
     *   @Enabled(TRUE),
     * })
     */
    public function actionAllFalse()
    {
    }

    /**
     * @Either({
     *   @All
     * })
     */
    public function actionAllInner()
    {
    }
}
