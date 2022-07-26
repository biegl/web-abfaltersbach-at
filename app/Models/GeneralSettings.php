<?php

namespace App\Models;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $is_proxy_card_feature_available;

    public static function group(): string
    {
        return 'general';
    }
}
