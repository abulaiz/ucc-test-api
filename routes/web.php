<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group(['prefix' => 'api'], function () use ($router) {
	$router->group(['prefix' => 'v1'], function () use ($router) {
		
		$router->get('vehicle', [
			'uses' => 'VehicleController@index',
			'as' => 'v1.vehicle.index'
		]);

		$router->post('vehicle', [
			'uses' => 'VehicleController@store',
			'as' => 'v1.vehicle.store'
		]);

		$router->get('vehicle/option/{option_type}', 'VehicleController@getOption');

	});
});