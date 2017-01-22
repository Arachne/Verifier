<?php

/*
 * This file is part of the Arachne package.
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Exception;

use Arachne\Verifier\RuleInterface;
use Exception;
use Nette\Application\BadRequestException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class VerificationException extends BadRequestException
{
    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * @param RuleInterface $rule
     * @param string        $message
     * @param Exception     $previous
     */
    public function __construct(RuleInterface $rule, $message, Exception $previous = null)
    {
        parent::__construct($message, $rule->getCode(), $previous);
        $this->rule = $rule;
    }

    /**
     * @return RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }
}
