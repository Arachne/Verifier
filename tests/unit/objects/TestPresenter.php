<?php

namespace Tests\Unit;

use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek
 *
 * @TestAnnotation
 */
class TestPresenter extends Presenter
{

	protected $property;

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

	/**
	 * @TestAnnotation
	 */
	public function createComponentComponent()
	{
	}

}
