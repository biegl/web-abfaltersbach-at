<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateAnalyticsStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Google Analytics Statistics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AnalyticsService $service)
    {
        $this->info('Updating Analytics data...');

        $year = Carbon::today()->year;
        $month = Carbon::today()->month;

        $service->retrieveAnalyticsData($year, $month);

        $this->info('✅ Analytics data has been updated.');
    }
}
