<?php

/*
 * This file is part of the Arachne package.
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Rules;

use Arachne\Verifier\RuleInterface;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @Annotation
 */
class Either implements RuleInterface
{
    /** @var \Arachne\Verifier\RuleInterface[] */
    public $rules = [];

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->rules ? reset($this->rules)->getCode() : 404;
    }
}
