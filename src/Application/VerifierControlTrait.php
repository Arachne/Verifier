<?php

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
    public function checkRequirements($reflection)
    {
        $this->getPresenter()->getVerifier()->checkReflection($reflection, $this->getPresenter()->getRequest(), $this->getUniqueId());
    }

    /**
     * Redirects to destination if all the requirements are met.
     *
     * @param int     $code
     * @param string  $destination
     * @param mixed[] $parameters
     */
    public function redirectVerified($code, $destination = null, $parameters = [])
    {
        // first parameter is optional
        if (!is_numeric($code)) {
            $parameters = is_array($destination) ? $destination : array_slice(func_get_args(), 1);
            $destination = $code;
            $code = null;
        } elseif (!is_array($parameters)) {
            $parameters = array_slice(func_get_args(), 2);
        }

        $presenter = $this->getPresenter();
        $link = $presenter->createRequest($this, $destination, $parameters, 'redirect');
        if ($presenter->getVerifier()->isLinkVerified($presenter->getLastCreatedRequest(), $this)) {
            $presenter->redirectUrl($link, $code);
        }
    }

    /**
     * Returns link to destination but only if all its requirements are met.
     *
     * @param string $destination
     * @param array  $args
     *
     * @return string|null
     */
    public function linkVerified($destination, $args = [])
    {
        $link = $this->link($destination, $args);
        $presenter = $this->getPresenter();

        if ($presenter->getVerifier()->isLinkVerified($presenter->getLastCreatedRequest(), $this)) {
            return $link;
        }
    }

    /**
     * Returns specified component but only if all its requirements are met.
     *
     * @param string $name
     *
     * @return IComponent|null
     */
    public function getComponentVerified($name)
    {
        $presenter = $this->getPresenter();

        if ($presenter->getVerifier()->isComponentVerified($name, $presenter->getRequest(), $this)) {
            return $this->getComponent($name);
        }
    }

    /**
     * Component factory. Delegates the creation of components to a createComponent<Name> method.
     *
     * @param string $name
     *
     * @return IComponent|null
     */
    protected function createComponent($name)
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
     * @param IComponent
     */
    protected function attached($component)
    {
        if ($component instanceof Presenter) {
            $this->onPresenter($component);
        }

        parent::attached($component);
    }
}
