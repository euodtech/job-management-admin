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
});

$router->group([
    'prefix' => 'myapi/',
], function ($app) {
    $app->post('login', 'AuthController@login');
    $app->post('forgot-password', 'AuthController@forgot_password');

    $app->get('check-type-company/{companyID}', 'AuthController@check_company_driver');
});

$router->group([
    'prefix' => 'myapi/',
    'middleware' => 'auth',
], function ($app) {

    //Session Page
    $app->get('get-user/{UserID}', 'UserController@get_user');

    $app->get('get-job', 'JobController@get_job');
    $app->get('get-job-by-user/{UserID}', 'JobController@get_job_by_user');

    // cancel job
    $app->post('cancel-job/{jobID}', 'JobController@cancel_job');

    // reschedule Job
    $app->post('reschedule-job/{jobID}', 'JobController@reschedule_job');

    $app->get('get-job-ongoing/{user_id}', 'JobController@get_job_ongoing');

    $app->post('finished-job', 'JobController@finished_job');

    $app->post('driver-get-job', 'JobController@driver_get_job');

    $app->get('get-list-job', 'DapaController@farhan');
    $app->get('get-list-job-byid', 'DapaController@getListJobID');
    $app->post('insert-job', 'DapaController@InsertJob');
    $app->get('delete-job/{id}', 'DapaController@DeleteJob');
    $app->post('update-job/{id}', 'DapaController@UpdateJob');



});