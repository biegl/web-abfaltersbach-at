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

Route::middleware('auth:api', 'cors')->group(function () {
    Route::resource('news', 'Api\NewsController');
    Route::post('news/{news}/attach', 'Api\NewsController@attachFile');
    Route::resource('files', 'Api\FilesController');
    Route::post('events/{event}/attach', 'Api\EventsController@attachFile');
    Route::resource('events', 'Api\EventsController');
    Route::resource('pages', 'Api\PagesController');
});

Route::middleware('auth:api', 'cors', 'isAdmin')->group(function () {
    Route::post('users/{user}/revoke', 'Api\UsersController@revoke');
    Route::resource('users', 'Api\UsersController');
});

Route::middleware('cors')->group(function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout');
});
