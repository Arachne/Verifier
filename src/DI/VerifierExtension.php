<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\DI;

use Arachne\DIHelpers\CompilerExtension;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class VerifierExtension extends CompilerExtension
{

	const TAG_HANDLER = 'arachne.verifier.ruleHandler';
	const TAG_PROVIDER = 'arachne.verifier.ruleProvider';

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$extension = $this->getExtension('Arachne\DIHelpers\DI\DIHelpersExtension');
		$providerResolver = $extension->addResolver(self::TAG_PROVIDER, 'Arachne\Verifier\RuleProviderInterface');

		$builder->addDefinition($this->prefix('chainRuleProvider'))
			->setClass('Arachne\Verifier\ChainRuleProvider')
			->setArguments(array(
				'providerResolver' => '@' . $providerResolver,
			));

		$handlerResolver = $extension->addResolver(self::TAG_HANDLER, 'Arachne\Verifier\RuleHandlerInterface');

		$builder->addDefinition($this->prefix('verifier'))
			->setClass('Arachne\Verifier\Verifier')
			->setArguments(array(
				'handlerResolver' => '@' . $handlerResolver,
			));

		$builder->addDefinition($this->prefix('annotationsRuleProvider'))
			->setClass('Arachne\Verifier\RuleProviderInterface')
			->setFactory('Arachne\Verifier\Annotations\AnnotationsRuleProvider')
			->addTag(self::TAG_PROVIDER)
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('cascadeRuleHandler'))
			->setClass('Arachne\Verifier\Rules\CascadeRuleHandler')
			->addTag(self::TAG_HANDLER, array(
				'Arachne\Verifier\Rules\Either',
				'Arachne\Verifier\Rules\All',
			));

		$extension = $this->getExtension('Nette\Bridges\Framework\NetteExtension');
		if ($extension) {
			$builder->getDefinition($extension->prefix('latteFactory'))
				->addSetup('?->onCompile[] = function($engine) { \Arachne\Verifier\Latte\VerifierMacros::install($engine->getCompiler()); }', array('@self'));
		}
	}

}
