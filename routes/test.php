<?php

use Illuminate\Support\Facades\Route;

Route::get('/unauthenticated', function () {
    return response()->json('Accept: application/json header missing', 400);
}); 