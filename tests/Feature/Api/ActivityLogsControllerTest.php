<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('it returns a list of activity logs for an admin', function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin, 'sanctum');

    activity()->log('some activity');

    $response = getJson('/api/activities');

    $response->assertSuccessful();
});

test('it returns a 401 for a non-admin user', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    activity()->log('some activity');

    $response = getJson('/api/activities');

    $response->assertUnauthorized();
});
