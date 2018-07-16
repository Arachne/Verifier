<?php

declare(strict_types=1);

namespace Arachne\Verifier;

use Arachne\Verifier\Exception\InvalidArgumentException;
use Arachne\Verifier\Exception\UnexpectedTypeException;
use Arachne\Verifier\Exception\VerificationException;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\UI\Component;
use Nette\Application\UI\ComponentReflection;
use Nette\Application\UI\Presenter;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class Verifier
{
    /**
     * @var RuleProviderInterface
     */
    private $ruleProvider;

    /**
     * @var callable
     */
    private $handlerResolver;

    /**
     * @var IPresenterFactory
     */
    private $presenterFactory;

    /**
     * @var RuleInterface[][]
     */
    private $cache;

    public function __construct(RuleProviderInterface $ruleProvider, callable $handlerResolver, IPresenterFactory $presenterFactory)
    {
        $this->ruleProvider = $ruleProvider;
        $this->handlerResolver = $handlerResolver;
        $this->presenterFactory = $presenterFactory;
    }

    /**
     * Returns rules that are required for given reflection.
     *
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty $reflection
     *
     * @return RuleInterface[]
     */
    public function getRules(Reflector $reflection): array
    {
        if ($reflection instanceof ReflectionMethod) {
            $key = $reflection->getDeclaringClass()->getName().'::'.$reflection->getName();
        } elseif ($reflection instanceof ReflectionClass) {
            $key = $reflection->getName();
        } elseif ($reflection instanceof ReflectionProperty) {
            $key = $reflection->getDeclaringClass()->getName().'::$'.$reflection->getName();
        } else {
            throw new InvalidArgumentException('Reflection must be an instance of either ReflectionMethod, ReflectionClass or ReflectionProperty.');
        }

        if (!isset($this->cache[$key])) {
            $this->cache[$key] = $this->ruleProvider->getRules($reflection);
        }

        return $this->cache[$key];
    }

    /**
     * Checks whether the given rules are met.
     *
     * @param RuleInterface[] $rules
     *
     * @throws VerificationException
     */
    public function checkRules(array $rules, Request $request, ?string $component = null): void
    {
        foreach ($rules as $rule) {
            $class = get_class($rule);
            $handler = ($this->handlerResolver)($class);
            if (!$handler instanceof RuleHandlerInterface) {
                throw new UnexpectedTypeException("No rule handler found for type '$class'.");
            }
            $handler->checkRule($rule, $request, $component);
        }
    }

    /**
     * Checks whether all rules of the given reflection are met.
     *
     * @param ReflectionClass|ReflectionMethod $reflection
     *
     * @throws VerificationException
     */
    public function checkReflection(Reflector $reflection, Request $request, ?string $component = null): void
    {
        $rules = $this->getRules($reflection);
        $this->checkRules($rules, $request, $component);
    }

    /**
     * Checks whether it is possible to run the given request.
     */
    public function isLinkVerified(Request $request, Component $component): bool
    {
        try {
            $parameters = $request->getParameters();
            if (isset($parameters[Presenter::SIGNAL_KEY])) {
                // No need to check anything else, the requirements for presenter,
                // action and component had to be met for the current request.
                $signalId = $parameters[Presenter::SIGNAL_KEY];
                if (!is_string($signalId)) {
                    throw new InvalidArgumentException('Signal name is not a string.');
                }

                $pos = strrpos($signalId, '-');
                if ($pos) {
                    // signal for a component
                    $name = $component->getUniqueId();
                    if ($name !== substr($signalId, 0, $pos)) {
                        throw new InvalidArgumentException("Wrong signal receiver, expected '".substr($signalId, 0, $pos)."' component but '$name' was given.");
                    }
                    $reflection = new ComponentReflection($component);
                    $signal = substr($signalId, $pos + 1);
                } else {
                    // signal for presenter
                    $name = null;
                    $presenter = $request->getPresenterName();
                    $reflection = new ComponentReflection($this->presenterFactory->getPresenterClass($presenter));
                    $signal = $signalId;
                }

                // Signal requirements
                $method = 'handle'.$signal;
                if ($reflection->hasCallableMethod($method)) {
                    $this->checkReflection($reflection->getMethod($method), $request, $name);
                }
            } else {
                $presenter = $request->getPresenterName();
                $reflection = new ComponentReflection($this->presenterFactory->getPresenterClass($presenter));

                // Presenter requirements
                $this->checkReflection($reflection, $request);

                // Action requirements
                $action = $parameters[Presenter::ACTION_KEY];
                $method = 'action'.$action;
                if ($reflection->hasCallableMethod($method)) {
                    $this->checkReflection($reflection->getMethod($method), $request);
                }
            }
        } catch (VerificationException $e) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the parent component can create the subcomponent with given name.
     */
    public function isComponentVerified(string $name, Request $request, Component $parent): bool
    {
        $reflection = new ComponentReflection($parent);
        $method = 'createComponent'.ucfirst($name);
        if ($reflection->hasMethod($method)) {
            $factory = $reflection->getMethod($method);
            try {
                $this->checkReflection($factory, $request, (bool) $parent->getParent() ? $parent->getUniqueId() : null);
            } catch (VerificationException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets public properties of the component to true or false according to their associated rules (if any).
     */
    public function verifyProperties(Request $request, Component $component): void
    {
        $reflection = new ReflectionClass($component);
        $id = (bool) $component->getParent() ? $component->getUniqueId() : null;

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $rules = $this->getRules($property);
            if (!(bool) $rules) {
                continue;
            }

            try {
                $this->checkRules($rules, $request, $id);
            } catch (VerificationException $e) {
                $property->setValue($component, false);
                continue;
            }

            $property->setValue($component, true);
        }
    }
}
