<?php

namespace Tests\Functional\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;

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
        $this->template->setFile(__DIR__.'/../../templates/block.latte');
        $this->template->render();
    }

    /**
     * @Enabled( "$parameter" )
     */
    public function handleSignal($parameter): void
    {
        $this->template->message = 'Signal called!';
    }
}
