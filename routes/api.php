<?php

use App\Http\Controllers\Api\GeneralSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('news', 'App\Http\Controllers\Api\NewsController');
    Route::post('news/{news}/attach', 'App\Http\Controllers\Api\NewsController@attachFile');
    Route::apiResource('files', 'App\Http\Controllers\Api\FilesController');
    Route::post('events/{event}/attach', 'App\Http\Controllers\Api\EventsController@attachFile');
    Route::apiResource('events', 'App\Http\Controllers\Api\EventsController');
    Route::post('pages/{page}/attach', 'App\Http\Controllers\Api\PagesController@attachFile');
    Route::apiResource('pages', 'App\Http\Controllers\Api\PagesController');
    Route::get('navigation', 'App\Http\Controllers\Api\NavigationController@index');
    Route::apiResource('persons', 'App\Http\Controllers\Api\PersonsController');
    Route::get('persons/list/{module}', 'App\Http\Controllers\Api\PersonsController@list');
    Route::post('persons/list/{module}', 'App\Http\Controllers\Api\PersonsController@saveList');
    Route::post('persons/{person}/attach', 'App\Http\Controllers\Api\PersonsController@attachFile');

    Route::get('analytics', 'App\Http\Controllers\Api\AnalyticsController@index');

    Route::group(['middleware' => 'isAdmin'], function () {
        Route::post('users/{user}/revoke', 'App\Http\Controllers\Api\UsersController@revoke');
        Route::apiResource('users', 'App\Http\Controllers\Api\UsersController');
        Route::get('activities', 'App\Http\Controllers\Api\ActivityLogsController@index');
        Route::put('settings', [GeneralSettingsController::class, 'update']);
    });

    Route::get('settings', [GeneralSettingsController::class, 'show']);
});

Route::post('login', 'App\Http\Controllers\Auth\LoginController@authenticate');
Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');

Route::fallback(function () {
    return response('Endpoint does not exist!', 404);
});
