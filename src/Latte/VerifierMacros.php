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
		$me->addMacro('ifComponentVerified', 'if ($_presenter->getContext()->getByType(\'Arachne\Verifier\Verifier\')->isComponentVerified(%node.word, $_presenter->getRequest(), $_control)):', 'endif');
		$me->addMacro('ifLinkVerified', '$_l->verifiedLink = $_control->link(%node.word, %node.array?); if (!$_presenter->getLastCreatedRequest() || $_presenter->getContext()->getByType(\'Arachne\Verifier\Verifier\')->isLinkVerified($_presenter->getLastCreatedRequest(), $_control)):', 'endif');
		$me->addMacro('ifPresenterLinkVerified', '$_l->verifiedLink = $_presenter->link(%node.word, %node.array?); if (!$_presenter->getLastCreatedRequest() || $_presenter->getContext()->getByType(\'Arachne\Verifier\Verifier\')->isLinkVerified($_presenter->getLastCreatedRequest(), $_presenter)):', 'endif');
		$me->addMacro('href', NULL, NULL, function (MacroNode $node, PhpWriter $writer) {
			$word = $node->tokenizer->fetchWord();
			$link = $word ? '$_control->link(' . $writer->formatWord($word) . ', %node.array?)' : '$_l->verifiedLink';
			return ' ?> href="<?php ' . $writer->write('echo %escape(%modify(' . $link . '))') . ' ?>"<?php ';
		});
	}

}
