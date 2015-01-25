<?php

namespace Tests\Unit\Classes;

use Nette\Application\UI\Presenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @TestRule
 */
class TestPresenter extends Presenter
{

	protected $property;

	/**
	 * @TestRule
	 */
	public function actionAction()
	{
	}

	/**
	 * This should be ignored.
	 * @TestRule
	 */
	public function renderAction()
	{
	}

	/**
	 * @TestRule
	 * @TestRule
	 */
	public function renderView()
	{
	}

	/**
	 * @TestRule
	 */
	public function handleSignal()
	{
	}

	/**
	 * @TestRule
	 */
	public function createComponentComponent()
	{
	}

}
