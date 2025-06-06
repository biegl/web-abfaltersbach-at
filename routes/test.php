<?php

use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
Route::post('/modules/{module}/save-list', [ModuleController::class, 'saveList'])->name('modules.saveList'); 