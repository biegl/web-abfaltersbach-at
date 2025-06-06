<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('users')->truncate();
});

test('it returns a list of users for an admin', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(5)->create();
    actingAs($admin, 'sanctum');

    $response = getJson('/api/users');

    $response->assertSuccessful();
    $response->assertJsonCount(6, 'data');
});

test('it does not allow a non-admin to list users', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $response = getJson('/api/users');

    $response->assertUnauthorized();
});

test('it returns a 401 for an unauthenticated user', function () {
    $response = getJson('/api/users');

    $response->assertUnauthorized();
});

test('it allows an admin to create a new user', function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin, 'sanctum');

    Notification::fake();

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 1,
    ];

    $response = postJson('/api/users', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', $data);

    $user = User::where('email', 'test@example.com')->first();
    Notification::assertSentTo($user, \App\Notifications\ResetPassword::class);
});

test('it does not allow a non-admin to create a new user', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 1,
    ];

    $response = postJson('/api/users', $data);

    $response->assertUnauthorized();
});

test('it allows an admin to show a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    actingAs($admin, 'sanctum');

    $response = getJson("/api/users/{$user->id}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['email' => $user->email]);
});

test('it does not allow a non-admin to show a user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    actingAs($user, 'sanctum');

    $response = getJson("/api/users/{$otherUser->id}");

    $response->assertUnauthorized();
});

test('it allows an admin to update a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    actingAs($admin, 'sanctum');

    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => 1,
    ];

    $response = putJson("/api/users/{$user->id}", $data);

    $response->assertSuccessful();
    $this->assertDatabaseHas('users', $data);
});

test('it does not allow a non-admin to update a user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    actingAs($user, 'sanctum');

    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => 1,
    ];

    $response = putJson("/api/users/{$otherUser->id}", $data);

    $response->assertUnauthorized();
});

test('it allows an admin to delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    actingAs($admin, 'sanctum');

    $response = deleteJson("/api/users/{$user->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('it does not allow a non-admin to delete a user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    actingAs($user, 'sanctum');

    $response = deleteJson("/api/users/{$otherUser->id}");

    $response->assertUnauthorized();
});

test('it allows an admin to revoke a user password', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    actingAs($admin, 'sanctum');

    \Illuminate\Support\Facades\Notification::fake();

    $response = postJson("/api/users/{$user->id}/revoke");

    $response->assertStatus(204);

    $user->refresh();
    $this->assertNull($user->password);

    \Illuminate\Support\Facades\Notification::assertSentTo($user, \App\Notifications\ResetPassword::class);
});

test('it does not allow a non-admin to revoke a user password', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    actingAs($user, 'sanctum');

    $response = postJson("/api/users/{$otherUser->id}/revoke");

    $response->assertUnauthorized();
});
