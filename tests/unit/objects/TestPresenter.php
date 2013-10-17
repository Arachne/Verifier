<?php

namespace Tests;

use Arachne\Verifier\Requirements;
use Exception;
use Nette\Application\UI\Presenter;

/**
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
