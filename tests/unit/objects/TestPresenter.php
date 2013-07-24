<?php

namespace Tests;

use Arachne\Verifier\Requirements;

/**
 * @TestAnnotation
 */
class TestPresenter extends \Nette\Application\UI\Presenter
{

	protected $property;

	final public function __construct()
	{
		throw new \Exception('This class is there for annotations only.');
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
