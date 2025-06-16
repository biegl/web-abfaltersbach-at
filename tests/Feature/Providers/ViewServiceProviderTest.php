<?php

use App\Providers\ViewServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

test('it shares the correct header image path for winter', function () {
    Carbon::setTestNow(Carbon::create(2023, 1, 15)); // A winter month

    $provider = new ViewServiceProvider(app());
    $provider->boot();

    $this->assertEquals('/images/header/abfaltersbach_winter.jpeg', View::shared('headerImagePath'));

    Carbon::setTestNow(); // Reset time
});

test('it shares the correct header image path for non-winter', function () {
    Carbon::setTestNow(Carbon::create(2023, 6, 15)); // A non-winter month

    $provider = new ViewServiceProvider(app());
    $provider->boot();

    $this->assertEquals('/images/header/abfaltersbach.jpeg', View::shared('headerImagePath'));

    Carbon::setTestNow(); // Reset time
});
