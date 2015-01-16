<?php

namespace Tests\Integration;

use Arachne\Codeception\ConfigFilesInterface;
use Codeception\TestCase\Test as BaseTest;

/**
 * @author Jáchym Toušek
 */
abstract class Test extends BaseTest implements ConfigFilesInterface
{

	public function getConfigFiles()
	{
		return [
			'config/config.neon',
		];
	}

}
