# Documentation

This extension is here to provide easy annotation-based verification whether given action is available or not. The most typical use-case is authorization (see Arachne/SecurityRules) but you can write your own handlers as well.


## Installation

The best way to install Arachne/Verifier is using [Composer](http://getcomposer.org/):

```sh
$ composer require arachne/verifier
```

Now you need to register Arachne/Verifier and Kdyby/Rules extensions using your [neon](http://ne-on.org/) config file.

```yml
extensions:
	kdyby.annotations: Kdyby\Annotations\DI\AnnotationsExtension
	arachne.verifier: Arachne\Verifier\DI\VerifierExtension
```

Please see documentation how to configure [Kdyby/Rules](https://github.com/Kdyby/Rules/blob/master/docs/en/index.md).

### PHP 5.4

Finally add Arachne\Verifier\Application\VerifierPresenterTrait trait to your BasePresenter and Arachne\Verifier\Application\VerifierControlTrait trait to your BaseControl.

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

### PHP 5.3

If you don't use PHP 5.4, just copy all methods from the traits to your BasePresenter and BaseControl.


## Usage

You need two things:
- some rule class(es) implementing Arachne\Verifier\RuleInterface
- a tagged service implementing Arachne\Verifier\RuleHandlerInterface

These will usually be provided by some other extensions. **For examples see Arachne/SecurityVerification and Arachne/ComponentsProtection.**

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

In presenters you can now use these rules to restrict access to the whole presenter or separately to its actions, signals and components. Note that rules for views are NOT supported.

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

Note that class-level annotations in components are not supported.

### Template

#### Links

In template you can use the `n:ifLinkVerified` macro to check whether the link is available.

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
