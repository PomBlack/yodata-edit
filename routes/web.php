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

$router->group(['prefix' => 'api'],function () use($router){
    $router->group(['prefix' => 'purchase'],function() use($router){
        $router->get("find","\App\Http\Controllers\EisController@find");
    });
    $router->get("test","\App\Http\Controllers\TestController@testAction");
});

