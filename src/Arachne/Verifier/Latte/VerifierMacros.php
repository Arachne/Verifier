<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Latte;

use Nette\Latte\Compiler;
use Nette\Latte\MacroNode;
use Nette\Latte\Macros\MacroSet;
use Nette\Latte\PhpWriter;

/**
 * @author Jáchym Toušek
 */
class VerifierMacros extends MacroSet
{

	/**
	 * @param Compiler $compiler
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('ifComponentVerified', 'if ($_presenter->getContext()->getByType(\'Arachne\Verifier\Verifier\')->isComponentVerified($_presenter->getRequest(), %node.word)):', 'endif');
		$me->addMacro('ifLinkVerified', '$_l->verifiedLink = $_control->link(%node.word, %node.array?); if (!$_presenter->getLastCreatedRequest() || $_presenter->getContext()->getByType(\'Arachne\Verifier\Verifier\')->isLinkVerified($_presenter->getLastCreatedRequest())):', 'endif');
		$me->addMacro('href', NULL, NULL, function (MacroNode $node, PhpWriter $writer) {
			$word = $node->tokenizer->fetchWord();
			if ($word) {
				return ' ?> href="<?php ' . $writer->write('echo %escape(%modify($_control->link(' . $writer->formatWord($word) . ', %node.array?)))') . ' ?>"<?php ';
			}
			return ' ?> href="<?php ' . $writer->write('echo %escape(%modify($_l->verifiedLink))') . ' ?>"<?php ';
		});
	}

}
