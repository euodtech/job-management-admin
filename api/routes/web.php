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

$router->get('/', function () use ($router) {
    return 'Euodoo Technologies Inc.';
});

$router->group([
    'prefix' => 'myapi/',
], function ($app) {
    $app->post('login', ['middleware' => 'throttle:5,1', 'uses' => 'AuthController@login']);
    $app->post('forgot-password', ['middleware' => 'throttle:3,5', 'uses' => 'AuthController@forgot_password']);
    $app->get('company-logo/{filename}', 'CompanyLogoController@show');
});

$router->group([
    'prefix' => 'myapi/',
    'middleware' => 'auth',
], function ($app) {

    //Session Page
    $app->get('get-user/{userID:[0-9]+}', 'UserController@get_user');

    $app->get('get-job', 'JobController@get_job');
    $app->get('get-job-by-user/{userID:[0-9]+}', 'JobController@get_job_by_user');

    // cancel job
    $app->post('cancel-job/{jobID:[0-9]+}', 'JobController@cancel_job');

    // reschedule Job
    $app->post('reschedule-job/{jobID:[0-9]+}', 'JobController@reschedule_job');
    $app->get('reschedule-status/{jobID:[0-9]+}', 'JobController@reschedule_status');

    $app->get('get-job-ongoing/{user_id:[0-9]+}', 'JobController@get_job_ongoing');

    $app->post('finished-job', 'JobController@finished_job');

    $app->post('driver-get-job', 'JobController@driver_get_job');

    $app->get('get-list-job', 'DapaController@farhan');
    $app->get('get-list-job-byid', 'DapaController@getListJobID');
    $app->post('insert-job', 'DapaController@InsertJob');
    $app->delete('delete-job/{id:[0-9]+}', 'DapaController@DeleteJob');
    $app->post('update-job/{id:[0-9]+}', 'DapaController@UpdateJob');

    // Change password
    $app->post('change-password', 'AuthController@change_password');

    // Update FCM token
    $app->post('update-fcm-token', 'AuthController@update_fcm_token');

    // Logout
    $app->post('logout', 'AuthController@logout');

    // Check company type (moved behind auth)
    $app->get('check-type-company/{companyID:[0-9]+}', 'AuthController@check_company_driver');

    // Traxroot proxy endpoints (credentials stay server-side)
    $app->post('traxroot/token', 'TraxrootProxyController@getToken');
    $app->get('traxroot/objects-status', 'TraxrootProxyController@getObjectsStatus');
    $app->get('traxroot/geozones', 'TraxrootProxyController@getGeozones');
});