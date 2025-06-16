<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Analytics\Facades\Analytics;
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
            $mostVisitedPages = Analytics::fetchMostVisitedPages($period, 5);
            $topBrowsers = Analytics::fetchTopBrowsers($period, 5);
            $topReferrers = Analytics::fetchTopReferrers($period, 5);
            $userTypes = Analytics::fetchUserTypes($period);

            // Data for previous month
            $startDate = $startDate->subMonth();
            $endDate = $startDate->copy()->lastOfMonth();
            $period = Period::create($startDate, $endDate);

            $previousAnalyticsData = $this->getMetrics($period);

            return [
                'previousMonth' => $previousAnalyticsData,
                'requestedMonth' => $requestedAnalyticsData,
                'mostVisitedPages' => $this->toArray($mostVisitedPages),
                'topBrowsers' => $this->toArray($topBrowsers),
                'topReferrers' => $this->toArray($topReferrers),
                'userTypes' => $this->toArray($userTypes),
            ];
        });
    }

    protected function getMetrics(Period $period)
    {
        $data = Analytics::get(
            $period,
            ['activeUsers', 'sessions', 'bounceRate', 'averageSessionDuration', 'screenPageViews'],
            ['pageTitle']
        );

        $data = $data instanceof Collection ? $data->first() : $data[0];

        return [
            'visitors' => (int) $data['activeUsers'],
            'sessions' => (int) $data['sessions'],
            'bounceRate' => (float) $data['bounceRate'] / 100,
            'avgSessionDurationInSeconds' => (int) $data['averageSessionDuration'],
        ];
    }

    protected function toArray($data)
    {
        return $data instanceof Collection ? $data->toArray() : $data;
    }
}
