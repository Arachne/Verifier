<?php

namespace Tests\Integration\Classes;

use Arachne\Verifier\IRule;
use Arachne\Verifier\IRuleHandler;
use Nette\Application\Request;
use Nette\Object;

/**
 * @author Jáchym Toušek
 */
class EnabledRuleHandler extends Object implements IRuleHandler
{

	/**
	 * @param IRule $rule
	 * @param Request $request
	 * @param string $component
	 * @throws DisabledException
	 */
	public function checkRule(IRule $rule, Request $request, $component = NULL)
	{
		if ($rule instanceof Enabled) {
			$this->checkRuleEnabled($rule, $request, $component);
		} else {
			throw new \InvalidArgumentException('Unknown rule \'' . get_class($rule) . '\' given.');
		}
	}

	/**
	 * @param Allowed $rule
	 * @throws DisabledException
	 */
	protected function checkRuleEnabled(Enabled $rule, Request $request, $component = NULL)
	{
		if (is_string($rule->value)) {
			$parameters = $request->getParameters();
			$enabled = (bool) $parameters[$component . '-' . ltrim($rule->value, '$')];
		} else {
			$enabled = $rule->value;
		}
		if (!$enabled) {
			throw new DisabledException("This action is not enabled.");
		}
	}

}
