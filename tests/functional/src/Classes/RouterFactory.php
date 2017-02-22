<?php

namespace Tests\Functional\Classes;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class RouterFactory
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
