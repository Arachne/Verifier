<?php

namespace Tests\Integration;

use Arachne\Verifier\Verifier;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Tests\Integration\Classes\CascadePresenter;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class CascadeRuleHandlerTest extends Test
{

	/** @var Verifier */
	private $verifier;

	public function _before()
	{
		parent::_before();
		$this->verifier = $this->guy->grabService(Verifier::class);
	}

	public function testEitherFirst()
	{
		$request = new Request('Cascade', 'GET', [
			Presenter::ACTION_KEY => 'eitherfirst',
		]);

		$this->assertTrue($this->verifier->isLinkVerified($request, new CascadePresenter()));
	}

	public function testEitherSecond()
	{
		$request = new Request('Cascade', 'GET', [
			Presenter::ACTION_KEY => 'eithersecond',
		]);

		$this->assertTrue($this->verifier->isLinkVerified($request, new CascadePresenter()));
	}

	public function testEitherFalse()
	{
		$request = new Request('Cascade', 'GET', [
			Presenter::ACTION_KEY => 'eitherfalse',
		]);

		$this->assertFalse($this->verifier->isLinkVerified($request, new CascadePresenter()));
	}

	public function testEitherInner()
	{
		$request = new Request('Cascade', 'GET', [
			Presenter::ACTION_KEY => 'eitherinner',
		]);

		$this->assertFalse($this->verifier->isLinkVerified($request, new CascadePresenter()));
	}

	public function testAllTrue()
	{
		$request = new Request('Cascade', 'GET', [
			Presenter::ACTION_KEY => 'alltrue',
		]);

		$this->assertTrue($this->verifier->isLinkVerified($request, new CascadePresenter()));
	}

	public function testAllFalse()
	{
		$request = new Request('Cascade', 'GET', [
			Presenter::ACTION_KEY => 'allfalse',
		]);

		$this->assertFalse($this->verifier->isLinkVerified($request, new CascadePresenter()));
	}

	public function testAllInner()
	{
		$request = new Request('Cascade', 'GET', [
			Presenter::ACTION_KEY => 'allinner',
		]);

		$this->assertTrue($this->verifier->isLinkVerified($request, new CascadePresenter()));
	}

}
