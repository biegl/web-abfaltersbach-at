<?php

use App\Models\GeneralSettings;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $settings = new GeneralSettings([
        'name' => 'Test Name',
        'address' => 'Test Address',
        'zip' => '54321',
        'city' => 'Test City',
        'email' => 'test@test.com',
        'is_proxy_card_feature_available' => true,
    ]);
    $settings->save();
});

test('it shows the general settings', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $response = getJson('/api/settings');

    $response->assertSuccessful();
    $response->assertJsonFragment(['name' => 'Test Name']);
});

test('it updates the general settings', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $data = [
        'name' => 'New Name',
        'address' => 'New Address',
        'zip' => '12345',
        'city' => 'New City',
        'email' => 'new-test@test.com',
        'is_proxy_card_feature_available' => false,
    ];

    $response = $this->putJson('/api/settings', $data);

    $response->assertSuccessful();

    $settings = new GeneralSettings();
    expect($settings->name)->toBe('New Name');
    expect($settings->email)->toBe('new-test@test.com');
});

test('it does not allow a non-admin to update the settings', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $data = [
        'name' => 'New Name',
        'address' => 'New Address',
        'zip' => '12345',
        'city' => 'New City',
        'email' => 'test@test.com',
        'is_proxy_card_feature_available' => false,
    ];

    $response = $this->putJson('/api/settings', $data);

    $response->assertForbidden();
}); 