<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class VerifierMacros extends MacroSet
{

	/**
	 * @param Compiler $compiler
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('ifComponentVerified', 'if ($_presenter->getVerifier()->isComponentVerified(%node.word, $_presenter->getRequest(), $_control)) {', '}');
		$me->addMacro('ifLinkVerified', 'if ($_verifiedLink = $_control->linkVerified(%node.word, %node.array?)) {', '}');
		$me->addMacro('ifPresenterLinkVerified', 'if ($_verifiedLink = $_presenter->linkVerified(%node.word, %node.array?)) {', '}');
		$me->addMacro('href', null, null, function (MacroNode $node, PhpWriter $writer) use ($me) {
			$word = $node->tokenizer->fetchWord();
			if ($word) {
				$link = '$_control->link(' . $writer->formatWord($word) . ', %node.array?)';
			} else {
				$node->modifiers .= '|safeurl';
				$link = '$_verifiedLink';
			}
			return ' ?> href="<?php ' . $writer->using($node, $me->getCompiler())->write('echo %escape(%modify(' . $link . '))') . ' ?>"<?php ';
		});
	}

}
