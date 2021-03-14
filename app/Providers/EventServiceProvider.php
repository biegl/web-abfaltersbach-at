<?php

namespace App\Providers;

use App\Listeners\NotificationSendingListener;
use App\Listeners\SetEmailVerificationDate;
use App\Models\Event;
use App\Models\News;
use App\Observers\EventObserver;
use App\Observers\NewsObserver;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSending;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PasswordReset::class => [
            SetEmailVerificationDate::class,
        ],
        NotificationSending::class => [
            NotificationSendingListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (config('app.env') == 'production') {
            News::observe(NewsObserver::class);
            Event::observe(EventObserver::class);
        }
    }
}
