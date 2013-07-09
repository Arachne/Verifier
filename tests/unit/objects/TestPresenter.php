<?php

namespace Test;

class TestPresenter extends \Nette\Application\UI\Presenter
{

	final public function __construct()
	{
		throw new \Exception('This class is there for annotations only.');
	}

	/**
	 *
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