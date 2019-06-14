<?php

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

$router->get('/', function () use ($router) {
});
$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('/regD','User\UserController@regD');
    $router->post('/loginD','User\UserController@loginD');
    $router->post('/passwordD','User\UserController@passwordD');
    $router->post('/weather','User\UserController@weather');
});
$router->group(['prefix' => 'curl'], function () use ($router) {
    $router->get('/guzzle','Curl\Curl1Controller@guzzle');
    $router->get('/get','Curl\Curl1Controller@getCurl');
    $router->get('/post','Curl\Curl1Controller@postCurl');
    $router->post('/postData','Curl\Curl1Controller@postData');
});
$router->group(['prefix' => 'pay'], function () use ($router) {
    $router->get('/pay','pay\PayContrller@pay');
});