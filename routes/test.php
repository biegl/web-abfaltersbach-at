<?php

use Illuminate\Support\Facades\Route;

Route::get('/unauthenticated', function () {
    return response()->json('Accept: application/json header missing', 400);
});

Route::post('/_test/store-file', function (\App\Http\Requests\StoreFile $request) {
    return response()->json(['message' => 'ok']);
}); 