Documentation
=============

This extension is here to provide easy annotation-based verification whether given action is available or not. The most tipical use-case is authorization (see Arachne/SecurityAnnotations) but you can write your own handlers as well.


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

Finally add the Arachne\Verifier\Application\TVerifierPresenter trait to your BasePresenter. You also probably want to overwrite the checkRequirements method, catch the exceptions thrown by Verifier and show some user-friendly flash messages.

```php
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	use \Arachne\Verifier\Application\TVerifierPresenter;
	
	/**
	 * @param \Nette\Reflection\ClassType|\Nette\Reflection\Method $element
	 */
	public function checkRequirements($reflection)
	{
		try {
			$this->verifier->checkAnnotations($reflection, $this->getRequest());
		} catch (\Arachne\Verifier\ForbiddenRequestException $e) {
			$this->flashMessage('This action is not allowed.', 'error');
			$this->redirect('Homepage:');
		}
	}

}
```

### PHP 5.3

If you don't use PHP 5.4, just copy the trait's methods to your BasePresenter and modify the checkRequirements method.


Usage
-----

You need three things:
- some annotation class(es) implementing Arachne\Verifier\IAnnotation
- a handler implementing Arachne\Verifier\IAnnotationHandler
- an exception extending the abstract Arachne\Verifier\ForbiddenRequestException and throw it from your handler if the action is not allowed

For examples see Arachne/SecurityAnnotations package.

Let's say you have some annotation App\MyAnnotation and a handler App\MyAnnotationHandler. See what you can do with them in the examples below.

### Configuration

You have to register the handler as a service in your config.neon.

```yml
services:
	- App\MyAnnotationHandler
```

### Presenter

In presenters you can now use these annotations to restrict access to presenter or action.

```php
use Arachne\Verifier\Requirements;
use App\MyAnnotation;

/**
 * @Requirements(@MyAnnotation("argument"))
 */
class ArticlePresenter extends BasePresenter
{

	/**
	 * @Requirements({
	 * 		@MyAnnotation("some argument"),
	 * 		@MyAnnotation("different argument")	 
	 * })
	 */
	public function actionEdit($id)
	{
		// ...
	}

}
```

These annotations also work for render* methods and handle* methods. Pleas note that render* method's annotation is NOT checked if the action* method with the same action name (the * part) exists.

### Template

In template you can use the `n:ifVerified` macro to check whether the action is available or not. The `n:href` macro without argument will automatically take the argument of the closest `n:ifVerified` macro so you don't need to write the argument twice.

```html
{* This link will not be shown if the action is not available. *} 
<a n:ifVerified="Article:edit $id" n:href>Link</a>
```
