<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/_test/protected-route', function () {
        return response()->json(['message' => 'ok']);
    })->middleware('auth');
});

test('unauthenticated user without json header gets redirected', function () {
    $this->get('/_test/protected-route')
        ->assertRedirect('/unauthenticated');
});

test('unauthenticated user without json header gets 400 after redirect', function () {
    $this->followingRedirects()
        ->get('/_test/protected-route')
        ->assertStatus(400)
        ->assertExactJson(['Accept: application/json header missing']);
});
