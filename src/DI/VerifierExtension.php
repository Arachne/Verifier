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
			->setArguments([
				'providerResolver' => '@' . $providerResolver,
			]);

		$handlerResolver = $extension->addResolver(self::TAG_HANDLER, 'Arachne\Verifier\RuleHandlerInterface');

		$builder->addDefinition($this->prefix('verifier'))
			->setClass('Arachne\Verifier\Verifier')
			->setArguments([
				'handlerResolver' => '@' . $handlerResolver,
			]);

		$builder->addDefinition($this->prefix('annotationsRuleProvider'))
			->setClass('Arachne\Verifier\RuleProviderInterface')
			->setFactory('Arachne\Verifier\Annotations\AnnotationsRuleProvider')
			->addTag(self::TAG_PROVIDER)
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('allRuleHandler'))
			->setClass('Arachne\Verifier\Rules\AllRuleHandler')
			->addTag(self::TAG_HANDLER, [
				'Arachne\Verifier\Rules\All',
			]);

		$builder->addDefinition($this->prefix('eitherRuleHandler'))
			->setClass('Arachne\Verifier\Rules\EitherRuleHandler')
			->addTag(self::TAG_HANDLER, [
				'Arachne\Verifier\Rules\Either',
			]);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$latte = $builder->getByType('Nette\Bridges\ApplicationLatte\ILatteFactory') ?: 'nette.latteFactory';
		if ($builder->hasDefinition($latte)) {
			$builder->getDefinition($latte)
				->addSetup('?->onCompile[] = function($engine) { \Arachne\Verifier\Latte\VerifierMacros::install($engine->getCompiler()); }', [ '@self' ]);
		}
	}

}
