<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new AnalyticsService;
    }

    public function index(Request $request)
    {
        $month = $request->query('month') ?? Carbon::today()->month;
        $year = $request->query('year') ?? Carbon::today()->year;

        $data = $this->service->retrieveAnalyticsData($year, $month);

        return response()->json($data);
    }
}
