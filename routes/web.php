<?php

declare(strict_types=1);

use App\Http\Controllers\FilesController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/startseite', [PageController::class, 'landing']);
Route::get('/files/{name}', [FilesController::class, 'download'])->name('download');
Route::fallback([PageController::class, 'show']);
