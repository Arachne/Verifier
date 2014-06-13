<?php

namespace Tests\Integration;

use Codeception\TestCase\Test;

class SafeUrlTest extends Test
{

	public function testHrefMacro()
	{
		$this->guy->amOnPage('/article/safeurl');
		$this->guy->seeResponseCodeIs(200);
		$this->guy->debugContent();
		$this->guy->seeLink('Safe url', '');
		$this->guy->dontSeeLink('Safe url', 'javascript');
	}

}
