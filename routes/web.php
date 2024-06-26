<?php

use App\Http\Controllers\FilesController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/startseite', [PageController::class, 'landing']);
Route::get('/files/{name}', [FilesController::class, 'download']);
Route::fallback([PageController::class, 'show']);
