<?php

/*
|--------------------------------------------------------------------------
| Application Routes
| By Danu Frmnsyh96.2024
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->get('/', function () use ($router) {
    return 'Made with pride by Unad.2024';
    // return redirect()->to('http://ortuseightdev.id/myapi/file/imagecustomer/square.jpeg');
    //$router->get('get-info','OrtusInfoController@get');
});

$router->group([
    'prefix' => 'myapi/',
], function ($app) {
    $app->post('login', 'AuthController@login');
    
    // $app->post('registration', 'AuthController@registration');
});

$router->group([
    'prefix' => 'myapi/',
    'middleware' => 'auth',
], function ($app) {

    //Session Page
    $app->get('get-user/{UserID}', 'UserController@get_user');

    $app->get('get-job', 'JobController@get_job');
    $app->get('get-job-by-user/{UserID}', 'JobController@get_job_by_user');

    $app->get('get-job-bayu/{user_id}', 'JobController@get_job_ongoing');
    

    $app->post('driver-get-job', 'JobController@driver_get_job');

    $app->get('get-list-job', 'DapaController@farhan');




});