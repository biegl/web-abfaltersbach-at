<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
    asAdmin();
});

it('lists users (5 seeded + admin)', function () {
    $this->getJson('api/users')
        ->assertStatus(200)
        ->assertJsonCount(6); // 5 seeded + the acting admin
});

it('creates a user', function () {
    $this->postJson('api/users', [
        'name' => 'New User', 'email' => 'new@example.com', 'role' => Role::USER,
    ])
        ->assertStatus(201)
        ->assertJson(['email' => 'new@example.com']);
});

it('validates a user on create', function () {
    $this->postJson('api/users', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'role']);
});

it('rejects a duplicate email on create', function () {
    User::factory()->create(['email' => 'dupe@example.com']);

    $this->postJson('api/users', ['name' => 'X', 'email' => 'dupe@example.com', 'role' => 1])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('shows a user', function () {
    $user = User::factory()->create();

    $this->getJson("api/users/{$user->id}")
        ->assertStatus(200)
        ->assertJson(['id' => $user->id]);
});

it('returns 404 for a missing user', function () {
    $this->getJson('api/users/999999')->assertStatus(404);
});

it('updates a user', function () {
    $user = User::factory()->create();

    $this->putJson("api/users/{$user->id}", ['name' => 'Renamed', 'email' => $user->email, 'role' => 1])
        ->assertStatus(200)
        ->assertJson(['name' => 'Renamed']);
});

it('validates a user on update', function () {
    $user = User::factory()->create();

    $this->putJson("api/users/{$user->id}", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'role']);
});

it('deletes a user', function () {
    $user = User::factory()->create();

    $this->deleteJson("api/users/{$user->id}")->assertStatus(204);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('revokes a user', function () {
    $user = User::factory()->create();

    $this->postJson("api/users/{$user->id}/revoke")->assertStatus(204);
});
