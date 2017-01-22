<?php

namespace Tests\Functional\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ParentControl extends Control
{
    use VerifierControlTrait;

    /**
     * @return ChildControl
     */
    protected function createComponentChild()
    {
        return new ChildControl();
    }
}
