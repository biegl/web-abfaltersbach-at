<?php

use App\Models\User;

it('registers a user successfully', function () {
    $this->postJson('api/register', [
        'name' => 'Max Muster',
        'email' => 'max@example.com',
        'password' => 'supersecret',
        'password_confirmation' => 'supersecret',
    ])
        ->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'name', 'email', 'created_at', 'updated_at', 'api_token']]);
});

it('requires name, email and password to register', function () {
    $this->postJson('api/register')
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                'name' => ['validation.required'],
                'email' => ['validation.required'],
                'password' => ['validation.required'],
            ],
        ]);
});

it('rejects registration when password confirmation does not match', function () {
    $this->postJson('api/register', [
        'name' => 'Max Muster',
        'email' => 'max@example.com',
        'password' => 'supersecret',
        'password_confirmation' => 'different',
    ])
        ->assertStatus(422)
        ->assertJson(['errors' => ['password' => ['Die Passwörter stimmen nicht überein']]]);
});

it('rejects registration with a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->postJson('api/register', [
        'name' => 'Max Muster',
        'email' => 'taken@example.com',
        'password' => 'supersecret',
        'password_confirmation' => 'supersecret',
    ])->assertStatus(422);
});

it('rejects registration with a too-short password', function () {
    $this->postJson('api/register', [
        'name' => 'Max Muster',
        'email' => 'max@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertStatus(422);
});
