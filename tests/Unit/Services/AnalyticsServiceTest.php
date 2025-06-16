<?php

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Spatie\Analytics\Facades\Analytics;

beforeEach(function () {
    Config::set('analytics.property_id', '123456789');
    Config::set('analytics.cache_lifetime_in_minutes', 60 * 24);
    Config::set('analytics.service_account_credentials_json', [
        'type' => 'service_account',
        'project_id' => 'test-project',
        'private_key_id' => 'test-key-id',
        'private_key' => 'test-key',
        'client_email' => 'test@example.com',
        'client_id' => '123456789',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/test%40example.com',
    ]);

    $this->service = new AnalyticsService;
});

test('it retrieves analytics data from cache if available', function () {
    $year = 2023;
    $month = 12;
    $cacheKey = "analytics_{$year}_{$month}";
    $expectedData = ['some' => 'data'];

    Cache::partialMock()
        ->shouldReceive('remember')
        ->once()
        ->with($cacheKey, 60 * 24 * 60, \Closure::class)
        ->andReturn($expectedData);

    $result = $this->service->retrieveAnalyticsData($year, $month);

    expect($result)->toBe($expectedData);
});

test('it retrieves fresh analytics data when not in cache', function () {
    $year = 2023;
    $month = 12;

    $cacheKey = "analytics_{$year}_{$month}";
    Cache::forget($cacheKey);

    // Set up fake responses for each Analytics method
    Analytics::fake([
        [
            'activeUsers' => 100,
            'sessions' => 150,
            'bounceRate' => 50.5,
            'averageSessionDuration' => 300,
            'screenPageViews' => 450,
            'pageTitle' => 'Home',
            'url' => '/page1',
            'browser' => 'Chrome',
            'pageReferrer' => 'google.com',
            'newVsReturning' => 'new',
        ]]);

    $result = $this->service->retrieveAnalyticsData($year, $month);

    expect($result)->toBeArray()
        ->and($result['requestedMonth'])->toBeArray()
        ->and($result['requestedMonth']['visitors'])->toBe(100)
        ->and($result['requestedMonth']['sessions'])->toBe(150)
        ->and($result['requestedMonth']['bounceRate'])->toBe(0.505)
        ->and($result['requestedMonth']['avgSessionDurationInSeconds'])->toBe(300)
        ->and($result['previousMonth'])->toBeArray()
        ->and($result['previousMonth']['visitors'])->toBe(100)
        ->and($result['previousMonth']['sessions'])->toBe(150)
        ->and($result['previousMonth']['bounceRate'])->toBe(0.505)
        ->and($result['previousMonth']['avgSessionDurationInSeconds'])->toBe(300)
        ->and($result['mostVisitedPages'])->toBeArray()
        ->and($result['mostVisitedPages'][0]['url'])->toBe('/page1')
        ->and($result['topBrowsers'])->toBeArray()
        ->and($result['topBrowsers'][0]['browser'])->toBe('Chrome')
        ->and($result['topReferrers'])->toBeArray()
        ->and($result['topReferrers'][0]['pageReferrer'])->toBe('google.com')
        ->and($result['userTypes'])->toBeArray()
        ->and($result['userTypes'][0]['newVsReturning'])->toBe('new');
});
