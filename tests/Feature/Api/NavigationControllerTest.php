<?php

use App\Models\Navigation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('it returns a paginated list of visible navigation items', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Navigation::factory()->count(25)->create(['refID' => null]);
    Navigation::factory()->count(5)->create(['refID' => null, 'navianzeigen' => 'Nein']);

    $response = getJson('/api/navigation');

    $response->assertSuccessful();
    $response->assertJsonCount(25, 'data');
});

test('it returns all navigation items when showAll is true', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Navigation::factory()->count(25)->create(['refID' => null]);
    Navigation::factory()->count(5)->create(['refID' => null, 'navianzeigen' => 'Nein']);

    $response = getJson('/api/navigation?showAll=true');

    $response->assertSuccessful();
    $response->assertJsonCount(30);
});
