<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Notifications\Channels\TelegramNotificationChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Telegram\Bot\Api;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        setlocale(LC_ALL, 'de_AT.UTF-8');
        Carbon::setLocale(config('app.locale'));

        // Register custom notification channel
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('telegram', function ($app) {
                return new TelegramNotificationChannel(
                    $app->make(Api::class)
                );
            });
        });
    }
}
