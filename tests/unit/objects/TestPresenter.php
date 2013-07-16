<?php

namespace Tests;

use Arachne\Verifier\Requirements;

/**
 * @Requirements(@TestAnnotation)
 */
class TestPresenter extends \Nette\Application\UI\Presenter
{

	protected $property;

	final public function __construct()
	{
		throw new \Exception('This class is there for annotations only.');
	}

	/**
	 * @Requirements({
	 *		@TestAnnotation,
	 *		@TestAnnotation,
	 * })
	 */
	public function actionAction()
	{
	}

	/**
	 * @Requirements({
	 *		@TestAnnotation,
	 *		@TestAnnotation,
	 * })
	 */
	public function renderView()
	{
	}

	/**
	 * @Requirements(@TestAnnotation)
	 */
	public function handleSignal()
	{
	}

}
