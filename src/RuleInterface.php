<?php

/*
 * This file is part of the Arachne package.
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
interface RuleInterface
{
    /**
     * Specifies HTTP code to use when the rule fails.
     *
     * @return int
     */
    public function getCode();
}
