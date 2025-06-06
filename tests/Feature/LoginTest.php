<?php

namespace Tests\Feature;

use App\Models\User;
use function Pest\Laravel\postJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('requires email and password', function () {
    postJson('api/login')
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                'email' => ['validation.required'],
                'password' => ['validation.required'],
            ],
        ]);
});

test('user logs in successfully', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'i-love-laravel'),
    ]);

    $response = postJson('api/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertStatus(200);
    $this->assertAuthenticatedAs($user);
    $response->assertJsonFragment(['email' => $user->email]);
});

test('user can log out', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = postJson('api/logout');

    $response->assertStatus(200);
    $this->assertGuest();
});

test('it returns an error with invalid credentials', function () {
    $user = User::factory()->create();

    $response = postJson('api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
    $response->assertJsonFragment(['email' => ['auth.failed']]);
});
