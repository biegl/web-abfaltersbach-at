<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

test('authenticated user is redirected from login page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/login');

    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('guest is not redirected from login page', function () {
    $response = $this->get('/login');

    $response->assertOk();
});
