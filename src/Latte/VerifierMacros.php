<?php

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
    public static function install(Compiler $compiler): void
    {
        $me = new static($compiler);
        $me->addMacro(
            'ifComponentVerified',
            'if ($this->global->uiPresenter->getVerifier()->isComponentVerified(%node.word, $this->global->uiPresenter->getRequest(), $this->global->uiControl)) {',
            '}'
        );
        $me->addMacro(
            'ifLinkVerified',
            'if ($_verifiedLink = $this->global->uiControl->linkVerified(%node.word, %node.array?)) {',
            '}'
        );
        $me->addMacro(
            'ifPresenterLinkVerified',
            'if ($_verifiedLink = $this->global->uiPresenter->linkVerified(%node.word, %node.array?)) {',
            '}'
        );
        $me->addMacro(
            'href',
            null,
            null,
            function (MacroNode $node, PhpWriter $writer) {
                $word = $node->tokenizer->fetchWord();
                if ($word) {
                    $link = '$this->global->uiControl->link('.$writer->formatWord($word).', %node.array?)';
                } else {
                    $node->modifiers .= '|safeurl';
                    $link = '$_verifiedLink';
                }

                return ' ?> href="<?php '.$writer->using($node)->write('echo %escape(%modify('.$link.'))').' ?>"<?php ';
            }
        );
    }
}
