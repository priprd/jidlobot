<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\CliRouter;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new CliRouter(array('action' => 'Cli:runTasks'));
		$router[] = new Route('geo?url=<requestedUrl .+>', [
			'presenter' => 'Geo',
			'action' => 'default',
			'requestedUrl' => [
				Route::VALUE => NULL,
				Route::FILTER_IN => NULL,
				Route::FILTER_OUT => NULL,
			],
		]);
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
