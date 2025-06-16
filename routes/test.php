<?php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/unauthenticated', function () {
    return response()->json('Accept: application/json header missing', 400);
});

Route::post('/_test/store-file', function (\App\Http\Requests\StoreFile $request) {
    return response()->json(['message' => 'ok']);
});

Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
Route::post('/modules/{id}/save-list', [ModuleController::class, 'saveList'])->name('modules.saveList');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');

Route::get('/login', function () {
    return 'login page';
})->middleware('guest')->name('login');
