<?php

namespace Tests\Integration;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * @author Jáchym Toušek
 */
class RouterFactory extends \Nette\Object
{

	/**
	 * @return IRouter
	 */
	public function create()
	{
		$router = new RouteList();
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}

}
