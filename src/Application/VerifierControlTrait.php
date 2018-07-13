<?php

declare(strict_types=1);

namespace Arachne\Verifier\Application;

use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
trait VerifierControlTrait
{
    /**
     * @var callable[]
     */
    public $onPresenter;

    /**
     * @param ReflectionClass|ReflectionMethod $reflection
     */
    public function checkRequirements($reflection): void
    {
        /** @var Presenter $presenter */
        $presenter = $this->getPresenter();
        $presenter->getVerifier()->checkReflection($reflection, $presenter->getRequest(), $this->getUniqueId());
    }

    /**
     * Redirects to destination if all the requirements are met.
     *
     * @param int          $code
     * @param string|array $destination
     * @param mixed[]      $parameters
     */
    public function redirectVerified($code, $destination = null, $parameters = []): void
    {
        // first parameter is optional
        if (!is_numeric($code)) {
            $parameters = is_array($destination) ? $destination : array_slice(func_get_args(), 1);
            $destination = $code;
            $code = null;
        } elseif (!is_array($parameters)) {
            $parameters = array_slice(func_get_args(), 2);
        }

        /** @var Presenter $presenter */
        $presenter = $this->getPresenter();
        $link = $presenter->createRequest($this, $destination, $parameters, 'redirect');
        if ($presenter->getVerifier()->isLinkVerified($presenter->getLastCreatedRequest(), $this)) {
            $presenter->redirectUrl($link, $code);
        }
    }

    /**
     * Returns link to destination but only if all its requirements are met.
     */
    public function linkVerified(string $destination, array $args = []): ?string
    {
        $link = $this->link($destination, $args);

        /** @var Presenter $presenter */
        $presenter = $this->getPresenter();

        return $presenter->getVerifier()->isLinkVerified($presenter->getLastCreatedRequest(), $this) ? $link : null;
    }

    /**
     * Returns specified component but only if all its requirements are met.
     */
    public function getComponentVerified(string $name): ?IComponent
    {
        /** @var Presenter $presenter */
        $presenter = $this->getPresenter();

        return $presenter->getVerifier()->isComponentVerified($name, $presenter->getRequest(), $this) ? $this->getComponent($name) : null;
    }

    /**
     * Component factory. Delegates the creation of components to a createComponent<Name> method.
     *
     * @param string $name
     */
    protected function createComponent($name): ?IComponent
    {
        $method = 'createComponent'.ucfirst($name);
        if (method_exists($this, $method)) {
            $this->checkRequirements($this->getReflection()->getMethod($method));
        }

        return parent::createComponent($name);
    }

    /**
     * Calls onPresenter event which is used to verify component properties.
     *
     * @param IComponent $component
     */
    protected function attached($component): void
    {
        if ($component instanceof Presenter) {
            $this->onPresenter($component);
        }

        parent::attached($component);
    }
}
