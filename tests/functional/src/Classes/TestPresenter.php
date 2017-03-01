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
     * @Either(@Enabled(true))
     */
    public function actionEitherFirst()
    {
    }

    /**
     * @Either({
     *   @Enabled(false),
     *   @Enabled(true),
     * })
     */
    public function actionEitherSecond()
    {
    }

    /**
     * @Either({
     *   @Enabled(false),
     *   @Enabled(false),
     * })
     */
    public function actionEitherFalse()
    {
    }

    /**
     * @Either({
     *   @Either()
     * })
     */
    public function actionEitherInner()
    {
    }

    /**
     * @All(@Enabled(true))
     */
    public function actionAllTrue()
    {
    }

    /**
     * @All({
     *   @Enabled(false),
     *   @Enabled(true),
     * })
     */
    public function actionAllFalse()
    {
    }

    /**
     * @Either({
     *   @All()
     * })
     */
    public function actionAllInner()
    {
    }
}
