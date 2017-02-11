# Documentation

This extension is here to provide easy annotation-based verification whether given action is available or not. The most typical use-case is authorization (see Arachne/SecurityVerification) but you can write your own handlers as well. It is not actually limited to annotations eiter, you can provide your own implementation to provide verification rules.


## Installation

The best way to install Arachne/Verifier is using [Composer](http://getcomposer.org/):

```sh
$ composer require arachne/verifier kdyby/annotations
```

Now you need to register required extensions using your [neon](http://ne-on.org/) config file.

```yml
extensions:
    arachne.servicecollections: Arachne\ServiceCollections\DI\ServiceCollectionsExtension
    arachne.verifier: Arachne\Verifier\DI\VerifierExtension
    kdyby.annotations: Kdyby\Annotations\DI\AnnotationsExtension
```

Please see documentation how to configure [Kdyby/Annotations](https://github.com/Kdyby/Annotations/blob/master/docs/en/index.md).

Finally add the `Arachne\Verifier\Application\VerifierPresenterTrait` trait to your presenters and the `Arachne\Verifier\Application\VerifierControlTrait` trait to the components where you need verfication.

```php
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    use \Arachne\Verifier\Application\VerifierPresenterTrait;
}

abstract class BaseControl extends \Nette\Application\UI\Control
{
    use \Arachne\Verifier\Application\VerifierControlTrait;
}
```


## Usage

To use Verifier you need two things:
- some rule class(es) implementing `Arachne\Verifier\RuleInterface`
- a tagged service implementing `Arachne\Verifier\RuleHandlerInterface`

These will usually be provided by some other extensions:

- Arachne/SecurityVerification - authentization and authorizationand
- Arachne/ComponentsProtection - bind component to certain action to fix common security hole
- Arachne/ParameterValidation - validate request parameters, works best with Arachne/EntityLoader

If you want to add your own rules, see the Configuration section below. Otherwise feel free to skip it.

### Configuration

Let's say you have some rule App\MyRule and a handler App\MyRuleHandler. You need to register the handler as a service in your config.neon, add the arachne.verifier.ruleHandler tag and configure the rule it handles in tag attributes. *Be sure to use the real rule classes names. An interface or a parent class will not work.*

```yml
services:
    myRuleHandler:
        class: App\MyRuleHandler
        tags:
            arachne.verifier.ruleHandler:
                - App\MyRule
```

### Presenter

In presenters you can now use these rules to restrict access to the whole presenter or separately to its actions, signals and components. Note that rules for views are NOT supported. Also class-level annotations for non-presenter components are NOT supported.

```php
use App\MyRule;

/**
 * @MyRule("argument")
 */
class ArticlePresenter extends BasePresenter
{
    /**
     * @MyRule("some argument")
     * @MyRule("different argument")
     */
    public function actionEdit($id)
    {
        // ...
    }

    /**
     * @MyRule("argument")
     */
    public function createComponentMenu()
    {
        // ...
    }
}
```

### Latte macros

#### Links

In latte template you can use the `n:ifLinkVerified` macro to check whether the link is available.

```html
{* This link will not be shown if the action is not available. *}
<a n:ifLinkVerified="Article:edit $id" n:href>Link</a>
```

In components you might need the `n:ifPresenterLinkVerified` macro as well. The difference is the same as between `{link ...}` and `{plink ...}` macros.

The `n:href` macro without argument will automatically take the argument of the last execuded `n:ifLinkVerified` or `n:ifPresenterLinkVerified` macro so you don't need to write the argument twice. If you do not use `Presenter::INVALID_LINK_EXCEPTION`, the condition will be true for invalid links.

#### Components

There is also the `n:ifComponentVerified` macro to check whether the component is available.

```html
{* The component will only be shown if it is available. *}
{ifComponentVerified menu}
    {control menu}
{/ifComponentVerified}
```

### Default rules

Verifier has built-in rules `All` and `Either`. They are no good by themselves but if you have some other rules you might need some more complicated conditions.

/**
 * This presenter is available only if MyRule("x") is met or both MyRule("y") and @MyRule("z") are met.
 * @Either(rules = {
 *     @MyRule("x"),
 *     @All(rules = {@MyRule("y"), @MyRule("z")})
 * })
 */
class ArticlePresenter extends BasePresenter
{
}
```

### Verified properties

Note: This feature is considered experimental.

Sometimes you might need to change some behavior instead of disabling the action completely based on outhorization or other conditions. Verifier can help you achieve this using verified properties. It is basically a public boolean property of a presenter or a component that has some verification rules. Verifier can go over these rules and set the property to true if all the rules are met or false otherwise.

```php
class ArticlePresenter extends BasePresenter
{
    /**
     * @MyRule("write")
     * @var bool
     */
    public $readAndWrite;
}

class HeaderControl extends BaseControl
{
    /**
     * @MyRule("adminLinks")
     * @var bool
     */
    public $adminLinks;
}

interface HeaderControlFactory
{
    /**
     * @return HeaderControl
     */
    public function create();
}
```

To get this working in a presenter you need to define the presenter as a service and also give it the `arachne.verifier.verifyProperties` tag.

For non-presenter components you need to create the component using a generated factory with the same tag.

```yml
services:
    articlePresenter:
        class: ArticlePresenter
        tags:
            - arachne.verifier.verifyProperties
    headerControlFactory:
        implement: HeaderControlFactory
        tags:
            - arachne.verifier.verifyProperties
```
