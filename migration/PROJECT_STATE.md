# Project State

This document outlines the current state of the project before the migration to Laravel 12.

## Laravel Version

- **Laravel Framework:** 9.x

## PHP Version

- **Required:** ^7.3|^8.0|^8.1|^8.2

## Dependencies

### Production Dependencies
- `deployer/deployer`: ^7.0
- `doctrine/dbal`: ^3.1
- `fruitcake/laravel-cors`: ^3
- `guzzlehttp/guzzle`: ^7
- `laravel-notification-channels/telegram`: ^3.0.0
- `laravel/framework`: ^9.0
- `laravel/sanctum`: ^3
- `laravel/tinker`: ^2
- `laravel/ui`: ^4.0
- `league/html-to-markdown`: ^5.0
- `sentry/sentry-laravel`: ^4
- `spatie/laravel-activitylog`: ^4.5
- `spatie/laravel-analytics`: ^4.0
- `spatie/laravel-settings`: ^2.4
- `vlucas/phpdotenv`: ^5.4

### Development Dependencies
- `driftingly/rector-laravel`: ^0.29.0
- `fakerphp/faker`: ^1.9.1
- `laravel/pint`: ^1.1
- `laravel/sail`: ^1
- `mockery/mockery`: ^1.4.2
- `nunomaduro/collision`: ^6.1
- `nunomaduro/larastan`: ^2.0
- `pestphp/pest`: ^1
- `pestphp/pest-plugin-laravel`: ^1
- `phpunit/phpunit`: ^9
- `rector/rector`: ^0.18.0
- `spatie/laravel-ignition`: ^1.0

## Testing Framework

- **PHPUnit:** ^9
- **Pest:** ^1

## Other notable configurations

- `laravel/ui` is used, which suggests blade templates with vue/react/bootstrap scaffolding.
- `sentry/sentry-laravel` for error tracking.
- `spatie` packages for activity logging, analytics and settings.
- `rector` and `larastan` are used in development, which is great for code quality. 