<?php

/**
 * This file is part of the Arachne Verifier extenstion
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\DI;

/**
 * @author Jáchym Toušek
 */
class VerifierExtension extends \Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('verifier'))
			->setClass('Arachne\Verifier\Verifier');

		$builder->addDefinition($this->prefix('securityAnnotationHandler'))
			->setClass('Arachne\Verifier\Security\SecurityAnnotationHandler');

		if ($builder->hasDefinition('nette.latte')) {
			$builder->getDefinition('nette.latte')
				->addSetup('Arachne\Verifier\Latte\VerifierMacros::install(?->getCompiler())', [ '@self' ]);
		}
	}

}
