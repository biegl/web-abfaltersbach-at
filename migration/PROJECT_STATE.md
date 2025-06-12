# Project State

This document outlines the current state of the project after the migration to Laravel 12.

## Laravel Version

- **Laravel Framework:** 12.17.0 (Upgraded from 9.x)

## PHP Version

- **Required:** ^8.4 (Upgraded from ^7.3|^8.0|^8.1|^8.2)

## Dependencies

### Production Dependencies
- `deployer/deployer`: ^7.0
- `doctrine/dbal`: ^3.1
- `guzzlehttp/guzzle`: ^7
- `laravel-notification-channels/telegram`: ^6.0 (Upgraded from ^3.0.0)
- `laravel/framework`: ^12.0 (Upgraded from ^9.0)
- `laravel/sanctum`: ^4.0 (Upgraded from ^3)
- `laravel/tinker`: ^2
- `laravel/ui`: ^4.0
- `league/html-to-markdown`: ^5.0
- `nunomaduro/collision`: ^8.1 (Added)
- `sentry/sentry-laravel`: ^4
- `spatie/laravel-activitylog`: ^4.0
- `spatie/laravel-analytics`: ^5.6 (Upgraded from ^4.0)
- `spatie/laravel-ignition`: ^2.0 (Moved from dev dependencies)
- `spatie/laravel-settings`: ^3.0 (Upgraded from ^2.4)
- `vlucas/phpdotenv`: ^5.4

### Development Dependencies
- `driftingly/rector-laravel`: ^2.0 (Upgraded from ^0.29.0)
- `fakerphp/faker`: ^1.9.1
- `larastan/larastan`: ^3.0 (Updated from nunomaduro/larastan ^2.0)
- `laravel/pint`: ^1.1
- `laravel/sail`: ^1
- `mockery/mockery`: ^1.4.4
- `pestphp/pest`: ^3.8 (Upgraded from ^1)
- `pestphp/pest-plugin-laravel`: ^3.2 (Upgraded from ^1)
- `phpunit/phpunit`: ^11.0 (Upgraded from ^9)
- `rector/rector`: ^2.0 (Upgraded from ^0.18.0)

## Testing Framework

- **PHPUnit:** ^11.0 (Upgraded from ^9)
- **Pest:** ^3.8 (Upgraded from ^1)

## Other notable configurations

- `laravel/ui` is used, which suggests blade templates with vue/react/bootstrap scaffolding.
- `sentry/sentry-laravel` for error tracking.
- `spatie` packages for activity logging, analytics and settings.
- `rector` and `larastan` are used in development, which is great for code quality.
- The application has been fully migrated to Laravel 12 and all tests are passing.
- The PHP requirement has been updated to ^8.4 to support Laravel 12.
- Some packages have been removed (fruitcake/laravel-cors) as they are no longer needed in Laravel 12. 