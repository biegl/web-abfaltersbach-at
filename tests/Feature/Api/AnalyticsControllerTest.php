<?php

use App\Models\User;
use App\Services\AnalyticsService;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('it returns analytics data', function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin, 'sanctum');

    $this->mock(AnalyticsService::class, function (MockInterface $mock) {
        $mock->shouldReceive('retrieveAnalyticsData')->once()->andReturn(['foo' => 'bar']);
    });

    $response = getJson('/api/analytics');

    $response->assertSuccessful();
    $response->assertJson(['foo' => 'bar']);
}); 