<?php

namespace App\Models;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $name = '';

    public string $address = '';

    public string $zip = '';

    public string $city = '';

    public string $email = '';

    public bool $is_proxy_card_feature_available = false;

    public static function group(): string
    {
        return 'general';
    }
}
