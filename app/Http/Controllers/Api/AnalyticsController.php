<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $service;

    // ponytail: resolve via container instead of `new` so it's swappable/mockable in tests
    public function __construct(AnalyticsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $month = $request->query('month') ?? Carbon::today()->month;
        $year = $request->query('year') ?? Carbon::today()->year;

        $data = $this->service->retrieveAnalyticsData($year, $month);

        return response()->json($data);
    }
}
