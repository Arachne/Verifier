<?php

declare(strict_types=1);

namespace Tests\Functional\Classes;

use Arachne\Verifier\Application\VerifierPresenterTrait;
use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ArticlePresenter extends Presenter
{
    use VerifierPresenterTrait;

    /**
     * @var BlockControlFactory
     * @inject
     */
    public $factory;

    /**
     * @var bool
     * @Enabled("$privilege")
     */
    public $privilege;

    /**
     * @Enabled(true)
     */
    public function actionDefault(): void
    {
        $this->getTemplate()->privilege = $this->privilege;
    }

    /**
     * @Enabled(false)
     *
     * @param int $id
     */
    public function actionEdit($id): void
    {
    }

    /**
     * @Enabled(true)
     *
     * @param int $id
     */
    public function actionModify($id): void
    {
        $this->redirectVerified('edit', $id);
    }

    /**
     * @Enabled(true)
     *
     * @param int $id
     */
    public function actionDelete($id): void
    {
    }

    /**
     * @Enabled(true)
     *
     * @param int $id
     */
    public function actionRemove($id): void
    {
        $this->redirectVerified('delete', $id);
    }

    /**
     * @Enabled(true)
     *
     * @param int $id
     */
    public function actionRedirect($id): void
    {
        $this->redirectVerified(301, 'delete', $id);
    }

    public function actionView(): void
    {
    }

    /**
     * @Enabled(true)
     */
    public function renderView(): void
    {
    }

    public function actionSafeurl(): void
    {
    }

    public function actionComponentNotEnabled(): void
    {
    }

    public function renderUndefinedAction(): void
    {
    }

    /**
     * @Enabled(true)
     */
    protected function createComponentHeader(): BlockControl
    {
        return $this->factory->create();
    }

    /**
     * @Enabled(false)
     */
    protected function createComponentFooter(): BlockControl
    {
        return new BlockControl();
    }

    /**
     * @Enabled(true)
     */
    protected function createComponentParent(): ParentControl
    {
        return new ParentControl();
    }

    public function formatTemplateFiles(): array
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':'.$name, ':'));

        return [__DIR__."/../../templates/$presenter.$this->view.latte"];
    }
}
