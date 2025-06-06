<?php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
Route::post('/modules/{module}/save-list', [ModuleController::class, 'saveList'])->name('modules.saveList');

Route::get('/news', [NewsController::class, 'index'])->name('news.index'); 