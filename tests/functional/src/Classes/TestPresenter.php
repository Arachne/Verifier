<?php

declare(strict_types=1);

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
    public function actionEitherFirst(): void
    {
    }

    /**
     * @Either({
     *   @Enabled(false),
     *   @Enabled(true),
     * })
     */
    public function actionEitherSecond(): void
    {
    }

    /**
     * @Either({
     *   @Enabled(false),
     *   @Enabled(false),
     * })
     */
    public function actionEitherFalse(): void
    {
    }

    /**
     * @Either({
     *   @Either()
     * })
     */
    public function actionEitherInner(): void
    {
    }

    /**
     * @All(@Enabled(true))
     */
    public function actionAllTrue(): void
    {
    }

    /**
     * @All({
     *   @Enabled(false),
     *   @Enabled(true),
     * })
     */
    public function actionAllFalse(): void
    {
    }

    /**
     * @Either({
     *   @All()
     * })
     */
    public function actionAllInner(): void
    {
    }
}
