<?php

namespace Tests\Functional;

use Codeception\TestCase\Test;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class PropertyTest extends Test
{

	public function testNoProperty()
	{
		$this->guy->amOnPage('/article/');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->dontSee('Presenter property verified');
		$this->guy->dontSee('Component property verified');
	}

	public function testPresenterProperty()
	{
		$this->guy->amOnPage('/article/?privilege=1');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->see('Presenter property verified');
		$this->guy->dontSee('Component property verified');
	}

	public function testComponentProperty()
	{
		$this->guy->amOnPage('/article/?header-privilege=1');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->dontSee('Presenter property verified');
		$this->guy->see('Component property verified');
	}

}
