<?php

namespace App\Services;

use Analytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Analytics\Period;

class AnalyticsService
{
    protected $cache_ttl;

    public function __construct()
    {
        $this->cache_ttl = config('analytics.cache_lifetime_in_minutes') * 60;
    }

    public function retrieveAnalyticsData($year, $month)
    {
        $cache_key = "analytics_{$year}_{$month}";

        return Cache::remember($cache_key, $this->cache_ttl, function () use ($year, $month) {
            // Data for requested month
            $startDate = Carbon::createFromDate($year, $month, 1);
            $endDate = $startDate->copy()->lastOfMonth();
            $period = Period::create($startDate, $endDate);

            $requestedAnalyticsData = $this->getMetrics($period);
            $mostVisitedPages = Analytics::fetchMostVisitedPages($period, 5)->toArray();
            $topBrowsers = Analytics::fetchTopBrowsers($period, 5)->toArray();
            $topReferrers = Analytics::fetchTopReferrers($period, 5)->toArray();
            $userTypes = Analytics::fetchUserTypes($period)->toArray();

            // Data for previous month
            $startDate = $startDate->subMonth();
            $endDate = $startDate->copy()->lastOfMonth();
            $period = Period::create($startDate, $endDate);

            $previousAnalyticsData = $this->getMetrics($period);

            return [
                'previousMonth' => $previousAnalyticsData,
                'requestedMonth' => $requestedAnalyticsData,
                'mostVisitedPages' => $mostVisitedPages,
                'topBrowsers' => $topBrowsers,
                'topReferrers' => $topReferrers,
                'userTypes' => $userTypes,
            ];
        });
    }

    protected function getMetrics(Period $period)
    {
        $totals = Analytics::performQuery(
            $period,
            implode(',', [
                'ga:visitors',
                'ga:newUsers',
                'ga:sessions',
                'ga:bounceRate',
                'ga:avgSessionDuration',
                'ga:organicSearches',
            ])
        )->totalsForAllResults;

        return [
            'visitors' => (int) $totals['ga:visitors'],
            'sessions' => (int) $totals['ga:sessions'],
            'bounceRate' => (float) $totals['ga:bounceRate'] / 100,
            'avgSessionDurationInSeconds' => (int) $totals['ga:avgSessionDuration'],
        ];
    }
}
