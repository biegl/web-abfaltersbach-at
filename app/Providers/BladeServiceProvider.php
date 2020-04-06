<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Add @safemail for obfuscating email addresses.
        // Example: @safemail('markus.buergler@me.com')
        Blade::directive('safemail', function ($expression) {
            $email   = '';
            for ($i = 0, $len = strlen($expression); $i < $len; $i++) {
                $j = random_int(0, 1);

                $email .= $j === 0 ? '&#' . ord($expression[$i]) . ';' : $expression[$i];
            }

            return str_replace('@', '&#64;', $email);
        });
    }
}
