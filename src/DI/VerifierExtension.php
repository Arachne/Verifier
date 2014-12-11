<?php

/**
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Verifier\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;

/**
 * @author Jáchym Toušek
 */
class VerifierExtension extends CompilerExtension
{

	const TAG_HANDLER = 'arachne.verifier.ruleHandler';
	const TAG_RULE_PROVIDER = 'arachne.verifier.ruleProvider';

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('verifier'))
			->setClass('Arachne\Verifier\Verifier');

		$builder->addDefinition($this->prefix('annotationsRuleProvider'))
			->setClass('Arachne\Verifier\RuleProviderInterface')
			->setFactory('Arachne\Verifier\Annotations\AnnotationsRuleProvider')
			->addTag(self::TAG_RULE_PROVIDER)
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('handlerResolverFactory'))
			->setFactory('Arachne\DI\Resolver\TagResolverFactory', array('tag' => self::TAG_HANDLER))
			->setAutowired(FALSE);

		if ($builder->hasDefinition('nette.latteFactory')) {
			$builder->getDefinition('nette.latteFactory')
				->addSetup('?->onCompile[] = function($engine) { \Arachne\Verifier\Latte\VerifierMacros::install($engine->getCompiler()); }', array('@self'));
		}
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$services = array();
		foreach ($builder->findByTag(self::TAG_RULE_PROVIDER) as $name => $_) {
			$services[] = '@' . $name;
		}

		$builder->getDefinition($this->prefix('verifier'))
			->setArguments(array(
				'ruleProviders' => $services,
				'handlerResolver' => new Statement('?->create()', array($this->prefix('@handlerResolverFactory'))),
			));
	}

}
