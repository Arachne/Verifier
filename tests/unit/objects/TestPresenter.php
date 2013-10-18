<?php

namespace Tests\Unit;

use Arachne\Verifier\Requirements;
use Exception;
use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek
 *
 * @TestAnnotation
 */
class TestPresenter extends Presenter
{

	protected $property;

	final public function __construct()
	{
		throw new Exception('This class is there for annotations only.');
	}

	/**
	 * @TestAnnotation
	 */
	public function actionAction()
	{
	}

	/**
	 * @TestAnnotation
	 */
	public function renderAction()
	{
	}

	/**
	 * @TestAnnotation
	 * @TestAnnotation
	 */
	public function renderView()
	{
	}

	/**
	 * @TestAnnotation
	 */
	public function handleSignal()
	{
	}

}
