<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $path = Cache::remember('header_image_path', 86400, function () {
            $current_month = Carbon::today()->month;

            return in_array($current_month, [1, 2, 3, 12])
                ? '/images/header/abfaltersbach_winter.jpeg'
                : '/images/header/abfaltersbach.jpeg';
        });

        View::share('headerImagePath', $path);
    }
}
