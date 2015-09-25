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
use Nette\Utils\AssertionException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class VerifierExtension extends CompilerExtension
{

	const TAG_HANDLER = 'arachne.verifier.ruleHandler';
	const TAG_PROVIDER = 'arachne.verifier.ruleProvider';
	const TAG_VERIFY_PROPERTIES = 'arachne.verifier.verifyProperties';

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		if ($extension = $this->getExtension('Arachne\DIHelpers\DI\ResolversExtension', false)) {
			$extension->add(self::TAG_HANDLER, 'Arachne\Verifier\RuleHandlerInterface');
		} elseif ($extension = $this->getExtension('Arachne\DIHelpers\DI\DIHelpersExtension', false)) {
			$extension->addResolver(self::TAG_HANDLER, 'Arachne\Verifier\RuleHandlerInterface');
		} else {
			throw new AssertionException('Cannot add resolver because arachne/di-helpers is not properly installed.');
		}

		if ($extension = $this->getExtension('Arachne\DIHelpers\DI\IteratorsExtension', false)) {
			$extension->add(self::TAG_PROVIDER, 'Arachne\Verifier\RuleProviderInterface');
		} elseif ($extension = $this->getExtension('Arachne\DIHelpers\DI\DIHelpersExtension', false)) {
			$extension->addResolver(self::TAG_PROVIDER, 'Arachne\Verifier\RuleProviderInterface');
		} else {
			throw new AssertionException('Cannot add iterator because arachne/di-helpers is not properly installed.');
		}

		$builder->addDefinition($this->prefix('chainRuleProvider'))
			->setClass('Arachne\Verifier\ChainRuleProvider');

		$builder->addDefinition($this->prefix('verifier'))
			->setClass('Arachne\Verifier\Verifier');

		$builder->addDefinition($this->prefix('annotationsRuleProvider'))
			->setClass('Arachne\Verifier\RuleProviderInterface')
			->setFactory('Arachne\Verifier\Annotations\AnnotationsRuleProvider')
			->addTag(self::TAG_PROVIDER)
			->setAutowired(false);

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

		if ($extension = $this->getExtension('Arachne\DIHelpers\DI\ResolversExtension', false)) {
			$handlerResolver = $extension->get(self::TAG_HANDLER);
		} elseif ($extension = $this->getExtension('Arachne\DIHelpers\DI\DIHelpersExtension', false)) {
			$handlerResolver = $extension->getResolver(self::TAG_HANDLER);
		}

		if ($extension = $this->getExtension('Arachne\DIHelpers\DI\IteratorsExtension', false)) {
			$providerIterator = $extension->get(self::TAG_PROVIDER);
		} elseif ($extension = $this->getExtension('Arachne\DIHelpers\DI\DIHelpersExtension', false)) {
			$providerIterator = $extension->getResolver(self::TAG_PROVIDER);
		}

		$builder->getDefinition($this->prefix('chainRuleProvider'))
			->setArguments([
				'providers' => '@' . $providerIterator,
			]);

		$builder->getDefinition($this->prefix('verifier'))
			->setArguments([
				'handlerResolver' => '@' . $handlerResolver,
			]);

		$latte = $builder->getByType('Nette\Bridges\ApplicationLatte\ILatteFactory') ?: 'nette.latteFactory';
		if ($builder->hasDefinition($latte)) {
			$builder->getDefinition($latte)
				->addSetup('?->onCompile[] = function ($engine) { \Arachne\Verifier\Latte\VerifierMacros::install($engine->getCompiler()); }', [ '@self' ]);
		}

		foreach ($builder->findByTag(self::TAG_VERIFY_PROPERTIES) as $service => $attributes) {
			$definition = $builder->getDefinition($service);
			if (is_subclass_of($definition->getClass(), 'Nette\Application\UI\Presenter')) {
				$definition->addSetup('$service->onStartup[] = function () use ($service) { ?->verifyProperties($service->getRequest(), $service); }', [ '@Arachne\Verifier\Verifier' ]);
			} else {
				$definition->addSetup('$service->onPresenter[] = function (\Nette\Application\UI\Presenter $presenter) use ($service) { ?->verifyProperties($presenter->getRequest(), $service); }', [ '@Arachne\Verifier\Verifier' ]);
			}
		}
	}

}
