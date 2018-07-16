<?php

declare(strict_types=1);

namespace Tests\Functional\Classes;

use Arachne\Verifier\Application\VerifierControlTrait;
use Nette\Application\UI\Control;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ChildControl extends Control
{
    use VerifierControlTrait;

    /**
     * @Enabled( "$parameter" )
     *
     * @param string $parameter
     */
    public function handleSignal1(string $parameter): void
    {
        $this->redirectVerified('signal2!');
    }

    public function handleSignal2(): void
    {
    }
}
