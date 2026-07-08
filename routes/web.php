<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ResetPasswordController;
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

// ResetPasswordController + resources/views/auth/passwords/reset.blade.php already exist
// (App\Models\User::sendPasswordResetNotification() depends on the "password.reset" route
// to build the emailed link) but were never wired up — restoring just these two, not the
// unrelated self-service "forgot password" request/email routes nothing currently triggers.
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Serves the built admin SPA shell for every /admin path. Must be a controller action, not a
// closure: php artisan route:cache (run on every deploy) cannot serialize closure routes.
Route::get('/admin/{any?}', [AdminController::class, 'index'])->where('any', '.*');

Route::fallback([PageController::class, 'show']);
