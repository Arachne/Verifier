<?php

declare(strict_types=1);

namespace Tests\Functional\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ParentControl extends Control
{
    use VerifierControlTrait;

    protected function createComponentChild(): ChildControl
    {
        return new ChildControl();
    }
}
