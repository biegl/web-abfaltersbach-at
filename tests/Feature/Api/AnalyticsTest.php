<?php

use App\Services\AnalyticsService;

beforeEach(fn () => asUser());

it('returns analytics data', function () {
    $this->mock(AnalyticsService::class, function ($mock) {
        $mock->shouldReceive('retrieveAnalyticsData')->andReturn([
            'previousMonth' => ['visitors' => 1, 'sessions' => 1, 'bounceRate' => 0.1, 'avgSessionDurationInSeconds' => 10],
            'requestedMonth' => ['visitors' => 2, 'sessions' => 2, 'bounceRate' => 0.2, 'avgSessionDurationInSeconds' => 20],
            'mostVisitedPages' => [],
            'topBrowsers' => [],
            'topReferrers' => [],
            'userTypes' => [],
        ]);
    });

    $this->getJson('api/analytics')
        ->assertStatus(200)
        ->assertJsonStructure(['previousMonth', 'requestedMonth', 'mostVisitedPages']);
});
