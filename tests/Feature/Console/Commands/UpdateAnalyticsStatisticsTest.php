<?php

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Mockery\MockInterface;

test('it updates analytics statistics and outputs success messages', function () {
    // Because the service is now injected into the handle method,
    // we don't need to mock the constructor. The service container
    // will use our mock when the handle method is called.
    $this->mock(AnalyticsService::class)
        ->shouldReceive('retrieveAnalyticsData')
        ->once()
        ->with(Carbon::today()->year, Carbon::today()->month);

    // Run the artisan command
    $this->artisan('analytics:update')
        ->expectsOutput('Updating Analytics data...')
        ->expectsOutput('✅ Analytics data has been updated.')
        ->assertExitCode(0);
});
