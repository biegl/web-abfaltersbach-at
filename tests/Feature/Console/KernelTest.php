<?php

use Illuminate\Console\Scheduling\Schedule;

test('it schedules the analytics update command to run daily', function () {
    $schedule = app(Schedule::class);

    $events = collect($schedule->events())->filter(function ($event) {
        return stripos($event->command, 'analytics:update');
    });

    if ($events->count() == 0) {
        $this->fail('The "analytics:update" command is not scheduled.');
    }

    expect($events->first()->expression)->toBe('0 0 * * *');
});
