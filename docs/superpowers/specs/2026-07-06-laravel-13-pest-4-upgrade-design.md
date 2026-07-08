# Upgrade Laravel 10 → 13, Pest 2 → 4

## Goal

Get Pest to true latest (v4.7.4). `pestphp/pest-plugin-laravel` v4 requires
Laravel `^12.52|^13.0` and PHP `^8.3`, so the Pest bump forces a Laravel major
upgrade.

## Confirmed facts

- Production PHP is 8.3.31 — clears Laravel 13's `^8.3` floor. Since Pest is
  `require-dev`, production's `composer install --no-dev` never evaluates
  Pest's constraint anyway; only `laravel/framework` and other `require`
  (non-dev) packages matter for production PHP.
- Laravel's own upgrade guide explicitly recommends **against** restructuring
  the app skeleton when moving a Laravel 10 app to 11+: "Laravel 11 has been
  carefully tuned to also support the Laravel 10 application structure."
  `app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`,
  and `App\Providers\RouteServiceProvider` stay as-is.
- Verified non-issues in this codebase (no code changes needed for these):
  - No `Doctrine\DBAL` usage anywhere in `app/` or `database/`.
  - No `HasUuids` trait usage (the Laravel 12 UUIDv7 change doesn't apply).
  - No `->change()` migrations and no `->double()`/`->float()`/
    `->unsignedDecimal()` etc. column definitions (Laravel 11's stricter
    column-modifier and floating-point rewrites don't apply).
  - `config/filesystems.php` already sets the `local` disk root explicitly to
    `storage_path('app')`, so Laravel 12's default-root-path change
    (`storage/app` → `storage/app/private`) doesn't affect us.
  - `app/Http/Middleware/VerifyCsrfToken.php` extends the framework's
    `VerifyCsrfToken`, which remains a deprecated-but-functional alias for
    `PreventRequestForgery` in Laravel 13.
  - No custom `spatie/laravel-settings` repository class (only rename
    surface in the v2→v3 upgrade).
  - Telegram notification usage (`app/Notifications/NewsCreated.php`,
    `app/Console/Commands/SendEventNotifications.php`) only uses
    `TelegramChannel`/`TelegramMessage::create()->to()->content()->button()`,
    none of which changed shape between v5 and v7.

## Package version targets

| Package | From | To | Why |
|---|---|---|---|
| `php` | `^7.3\|^8.0\|^8.1\|^8.2` | `^8.3` | Laravel 13 floor |
| `laravel/framework` | `^10.0` | `^13.0` | target |
| `laravel/sanctum` | `^3` | `^4.0` | required for L11+; publish sanctum migrations, update `config/sanctum.php` middleware keys per Laravel 11 upgrade guide |
| `laravel/tinker` | `^2` | `^3.0` | L13 support (v2 caps at L12) |
| `laravel-notification-channels/telegram` | `^5.0` | `^7.0` | v5 caps at L11, v7 needs L12+; no breaking API surface hit in this codebase |
| `spatie/laravel-settings` | `^2.4` | `^3.0` | v2 caps below L11; needs a settings-table migration (locked column default `null`→`false`, unique constraint on `['group','name']`) |
| `spatie/laravel-analytics` | `^5.6` (already staged uncommitted) | `^5.7` | 5.6 caps at L12, 5.7 adds L13 support — folds in the in-progress uncommitted bump |
| `spatie/laravel-activitylog` | `^4.5` | unchanged | v4.12.3 already supports L13 — no v5 jump needed (v5 requires PHP ^8.4, which we don't need) |
| `doctrine/dbal` | `^3.1` | removed | Laravel dropped the dependency in 11; unused in this codebase |
| `pestphp/pest` | `^2` | `^4.0` | the actual ask |
| `pestphp/pest-plugin-laravel` | `^2` | `^4.0` | pairs with Pest 4 |
| `phpunit/phpunit` | `^10` | `^12.0` | Pest 4 requires `^12.5.30` |
| `nunomaduro/collision` | `^7.0` | `^8.0` | Pest 4 requires `^8.9.4` |
| sentry-laravel, laravel/ui, laravel/nightwatch, laravel/ignition, laravel/pint, mockery, fakerphp/faker, laravel/sail, driftingly/rector-laravel | unchanged constraints | — | already span 10→13; `composer update` picks the latest compatible version |

## Other touches

- CI workflows (`test.yml`, `pint.yml`, `phpstan.yml`, `rector.yml`) and
  `deploy.yml`: PHP matrix `8.2` → `8.3`.
- `phpunit.xml`: schema needs checking against PHPUnit 12's expectations
  (verified empirically by running the suite, not assumed upfront).
- `tests/` (21 files): run once before touching anything to confirm the
  known-green baseline, then again after each commit, so any breakage is
  attributable to this upgrade and not pre-existing flakiness.

## Commit structure (single PR, staged commits)

1. Composer: bump all versions above in one `composer require
   --with-all-dependencies` pass, remove `doctrine/dbal`; fold in the
   already-uncommitted `spatie/laravel-analytics` / `config/analytics.php`
   changes sitting in the working tree rather than stashing them.
2. CI/deploy PHP version bump (8.2 → 8.3).
3. Code fixes surfaced by the bump: Sanctum config middleware keys, the
   settings v3 migration, any PHPUnit/Pest config schema fixes.
4. Pest v4 fixups if the 21 existing tests need syntax or config changes.

## Verification

- Run the full Pest suite before starting (baseline) and after each commit.
- `composer validate`.
- Boot smoke check (`php artisan about`) to confirm the app boots on Laravel
  13 without any skeleton changes.

## Explicitly out of scope

- Migrating to the Laravel 11+ streamlined skeleton (`bootstrap/app.php`
  restructure) — officially unnecessary and would inflate the diff for no
  functional gain.
- `spatie/laravel-activitylog` v5, `deployer/deployer` v8 — neither is
  required to reach the goal.
