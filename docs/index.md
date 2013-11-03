Documentation
=============

This extension is here to provide easy annotation-based verification whether given action is available or not. The most typical use-case is authorization (see Arachne/SecurityAnnotations) but you can write your own handlers as well.


Instalation
-----------

The best way to install Arachne/Verifier is using [Composer](http://getcomposer.org/):

```sh
$ composer require arachne/verifier:@dev
```

Now you need to register Arachne/Verifier and Kdyby/Annotations extensions using your [neon](http://ne-on.org/) config file.

```yml
extensions:
	kdyby.annotations: Kdyby\Annotations\DI\AnnotationsExtension
	arachne.verifier: Arachne\Verifier\DI\VerifierExtension
```

Please see documentation how to configure [Kdyby/Annotations](https://github.com/Kdyby/Annotations/blob/master/docs/en/index.md).

### PHP 5.4

Finally add the Arachne\Verifier\Application\TVerifierPresenter trait to your BasePresenter.

```php
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	use \Arachne\Verifier\Application\TVerifierPresenter;

}
```

### PHP 5.3

If you don't use PHP 5.4, just copy the trait's methods to your BasePresenter.


Usage
-----

You need three things:
- some annotation class(es) implementing Arachne\Verifier\IAnnotation
- a tagged service implementing Arachne\Verifier\IAnnotationHandler
- an exception extending the abstract Arachne\Verifier\ForbiddenRequestException and throw it from your handler if the action is not allowed

For examples see Arachne/SecurityAnnotations package.

Let's say you have some annotation App\MyAnnotation and a handler App\MyAnnotationHandler. See what you can do with them in the examples below.

### Configuration

You have to register the handler as a service in your config.neon, add the arachne.verifier.annotationHandler tag and configure the annotations it handles in tag attributes. Be sure to use the real annotation classes names. An interface or a parent class will not work.

```yml
services:
	myAnnotationHandler:
		class: App\MyAnnotationHandler
		tags:
			arachne.verifier.annotationHandler:
				- App\MyAnnotation
```

### Presenter

In presenters you can now use these annotations to restrict access to the whole presenter or separately to its actions, views, signals and components.

```php
use App\MyAnnotation;

/**
 * @MyAnnotation("argument")
 */
class ArticlePresenter extends BasePresenter
{

	/**
	 * @MyAnnotation("some argument")
	 * @MyAnnotation("different argument")
	 */
	public function actionEdit($id)
	{
		// ...
	}

	/**
	 * @MyAnnotation("argument")
	 */
	public function createComponentMenu()
	{
		// ...
	}

}
```

Note that this will only work in presenters. Annotations in component classes are not supported.

### Template

In template you can use the `n:ifLinkVerified` macro to check whether the link is available. The `n:href` macro without argument will automatically take the argument of the closest `n:ifLinkVerified` macro so you don't need to write the argument twice. If you do not use `Presenter::INVALID_LINK_EXCEPTION`, the condition will be true for invalid links.

```html
{* This link will not be shown if the action is not available. *}
<a n:ifLinkVerified="Article:edit $id" n:href>Link</a>
```

There is also the `n:ifComponentVerified` macro to check whether the component is available.
```html
{* The component will only be shown if it is available. *}
{ifComponentVerified menu}
	{control menu}
{/ifComponentVerified}
```
