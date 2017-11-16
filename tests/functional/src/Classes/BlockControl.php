<?php

declare(strict_types=1);

namespace Tests\Functional\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class BlockControl extends Control
{
    use VerifierControlTrait;

    /**
     * @var bool
     * @Enabled( "$privilege" )
     */
    public $privilege;

    public function render(): void
    {
        $this->getTemplate()->privilege = $this->privilege;

        /** @var ITemplate $template */
        $template = $this->template;

        $template->setFile(__DIR__.'/../../templates/block.latte');
        $template->render();
    }

    /**
     * @Enabled( "$parameter" )
     */
    public function handleSignal($parameter): void
    {
        $this->template->message = 'Signal called!';
    }
}
