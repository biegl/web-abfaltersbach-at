<?php

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
    Route::resource('news', 'App\Http\Controllers\Api\NewsController');
    Route::post('news/{news}/attach', 'App\Http\Controllers\Api\NewsController@attachFile');
    Route::resource('files', 'App\Http\Controllers\Api\FilesController');
    Route::post('events/{event}/attach', 'App\Http\Controllers\Api\EventsController@attachFile');
    Route::resource('events', 'App\Http\Controllers\Api\EventsController');
    Route::post('pages/{page}/attach', 'App\Http\Controllers\Api\PagesController@attachFile');
    Route::resource('pages', 'App\Http\Controllers\Api\PagesController');
    Route::resource('persons', 'App\Http\Controllers\Api\PersonsController');
    Route::get('persons/list/{module}', 'App\Http\Controllers\Api\PersonsController@list');
    Route::post('persons/list/{module}', 'App\Http\Controllers\Api\PersonsController@saveList');
    Route::post('persons/{person}/attach', 'App\Http\Controllers\Api\PersonsController@attachFile');
    Route::post('persons/{person}/delete/{file}', 'App\Http\Controllers\Api\PersonsController@deleteFile');

    Route::group(['middleware' => 'isAdmin'], function () {
        Route::post('users/{user}/revoke', 'App\Http\Controllers\Api\UsersController@revoke');
        Route::resource('users', 'App\Http\Controllers\Api\UsersController');
        Route::get('activities', 'App\Http\Controllers\Api\ActivityLogsController@index');
    });
});

Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');
Route::post('login', 'App\Http\Controllers\Auth\LoginController@authenticate');
Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');

Route::fallback(function () {
    return response('Endpoint does not exist!', 404);
});
