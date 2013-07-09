<?php

namespace Test;

class TestPresenter extends \Nette\Application\UI\Presenter
{

	final public function __construct()
	{
		throw new \Exception('This class is there for annotations only.');
	}

	/**
	 * @Requirements({
	 *		@InRole("admin")
	 *		@Allowed("")
	 * })
	 */
	public function actionAction()
	{
	}

	/**
	 *
	 */
	public function renderView()
	{
	}

	/**
	 *
	 */
	public function handleSignal()
	{
	}

}