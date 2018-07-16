<?php

declare(strict_types=1);

namespace Tests\Functional\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @property Template $template
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
