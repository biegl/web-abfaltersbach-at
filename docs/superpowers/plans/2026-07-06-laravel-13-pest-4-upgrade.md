# Laravel 10→13 / Pest 2→4 Upgrade Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Get Pest to latest (v4.7.4) by upgrading Laravel 10 → 13 and every package that gates on it, without restructuring the app skeleton.

**Architecture:** All composer version bumps land in a single resolved `composer.json`/`composer.lock` change — Laravel 13, Pest 4, and every package that (directly or via a locked version) constrains `laravel/framework` must update together in one command, or the solver reports a conflict. This was verified empirically during planning: a full trial run of the upgrade was performed and reverted, so every command and fix below is confirmed working, not speculative. App-skeleton files (`Http/Kernel.php`, `Console/Kernel.php`, `Exceptions/Handler.php`, `RouteServiceProvider`) are explicitly NOT touched — Laravel's own upgrade guide says the old structure keeps working through 11/12/13, and the trial run confirmed the app boots and the suite passes with zero skeleton changes.

**Tech Stack:** PHP 8.3, Laravel 13, Pest 4 / PHPUnit 12, Composer.

**Verified baseline:** with all changes in this plan applied, `vendor/bin/pest` produces `1 skipped, 125 passed` — identical to the pre-upgrade baseline (the 1 skip is a pre-existing, deliberate `orderByRaw FIELD() is MySQL-only` skip in `tests/Feature/Api/PersonsTest.php`, unrelated to this upgrade). `phpunit.xml` needed zero changes — PHPUnit 12.5.30 accepts the existing 10.5-schema config as-is.

## Global Constraints

- PHP floor: `^8.3` (production is confirmed running 8.3.31; Pest is `require-dev` only, so it never affects production's `composer install --no-dev`).
- `laravel/framework`: `^13.0`.
- `pestphp/pest`: `^4.0`, `pestphp/pest-plugin-laravel`: `^4.0`, `phpunit/phpunit`: `^12.0`, `nunomaduro/collision`: `^8.0`.
- Do NOT restructure the app skeleton (no `bootstrap/app.php` rewrite, no removing `app/Http/Kernel.php` / `app/Console/Kernel.php` / `app/Exceptions/Handler.php` / `App\Providers\RouteServiceProvider`) — explicitly out of scope per the design spec, and confirmed unnecessary.
- Single PR, staged commits — each task below is one commit.
- Full reference: `docs/superpowers/specs/2026-07-06-laravel-13-pest-4-upgrade-design.md`.

---

### Task 0: Capture the pre-upgrade baseline

**Files:** none (verification only).

**Interfaces:**
- Produces: a recorded pass/fail count to diff every later task's test run against.

- [ ] **Step 1: Run the full suite before touching anything**

Run: `vendor/bin/pest`
Expected: `Tests: 1 skipped, 125 passed (225 assertions)` (the skip is the pre-existing MySQL-only test in `tests/Feature/Api/PersonsTest.php`). Write this number down — every later task's suite run is compared against it, not against "all green" in the abstract.

---

### Task 1: Raise PHP floor to 8.3 (composer.json + CI/deploy workflows)

**Files:**
- Modify: `composer.json:11` (the `"php"` line)
- Modify: `.github/workflows/test.yml` (the `php-version: 8.2` line under "Setup PHP")
- Modify: `.github/workflows/pint.yml` (the `php-version: 8.2` line)
- Modify: `.github/workflows/phpstan.yml` (the `php-version: 8.2` line)
- Modify: `.github/workflows/rector.yml` (the `php-version: 8.2` line)
- Modify: `.github/workflows/deploy.yml` (the `php-version: 8.2` line)

**Interfaces:**
- Produces: `composer.json` with `"php": "^8.3"` under `require` — Task 3 depends on this being in place before the big dependency bump (Laravel 13 requires PHP `^8.3`, so the platform constraint must already allow it).

- [ ] **Step 1: Bump the PHP constraint in composer.json**

Edit `composer.json` line 11:

```json
"php": "^7.3|^8.0|^8.1|^8.2",
```

to:

```json
"php": "^8.3",
```

- [ ] **Step 2: Bump PHP version in all 5 GitHub Actions workflows**

In each of `.github/workflows/test.yml`, `.github/workflows/pint.yml`, `.github/workflows/phpstan.yml`, `.github/workflows/rector.yml`, `.github/workflows/deploy.yml`, find:

```yaml
          php-version: 8.2
```

and change to:

```yaml
          php-version: 8.3
```

(Each file has exactly one occurrence.)

- [ ] **Step 3: Verify no other file still pins PHP 8.2**

Run: `grep -rn "8\.2" .github/workflows/ composer.json`
Expected: no output (empty — confirms every reference was updated).

- [ ] **Step 4: Validate composer.json is still well-formed**

Run: `composer validate --no-check-all`
Expected: `./composer.json is valid`

- [ ] **Step 5: Commit**

```bash
git add composer.json .github/workflows/test.yml .github/workflows/pint.yml .github/workflows/phpstan.yml .github/workflows/rector.yml .github/workflows/deploy.yml
git commit -S -m "chore: raise PHP floor to 8.3 for Laravel 13 / Pest 4 upgrade"
```

---

### Task 2: Remove unused doctrine/dbal dependency

**Files:**
- Modify: `composer.json` (remove the `"doctrine/dbal": "^3.1"` line from `require`)
- Modify: `composer.lock` (regenerated by the command below)

**Interfaces:**
- Consumes: nothing from Task 1 except the already-updated `composer.json`.
- Produces: `doctrine/dbal` fully removed from the dependency tree — Task 3's big update must not need to re-add it.

- [ ] **Step 1: Confirm there is no code usage before removing**

Run: `grep -rln "Doctrine\\\\DBAL\|doctrine" app/ database/ config/`
Expected: no output (empty — confirms it's safe to remove; already verified during design research).

- [ ] **Step 2: Remove the package**

Run: `composer remove doctrine/dbal`
Expected: output ends with `Generating autoload files` and no errors; `doctrine/dbal` disappears from `composer.json`'s `require` block.

- [ ] **Step 3: Verify it's gone**

Run: `composer show doctrine/dbal`
Expected: `Package doctrine/dbal not found`

- [ ] **Step 4: Commit**

```bash
git add composer.json composer.lock
git commit -S -m "chore: remove unused doctrine/dbal dependency"
```

---

### Task 3: Bump Laravel framework and all dependent packages to their Laravel-13/Pest-4-compatible versions

**Files:**
- Modify: `composer.json` (`require` and `require-dev` sections)
- Modify: `composer.lock` (regenerated)
- Modify: `config/analytics.php` (already has an uncommitted change in the working tree bumping the comment "view id" → "property id" for the `spatie/laravel-analytics` 5.6→5.7 bump — fold it into this commit instead of losing it)

**Interfaces:**
- Consumes: `composer.json` with PHP `^8.3` (Task 1) and no `doctrine/dbal` (Task 2).
- Produces: `laravel/framework` at `v13.18.1`, `pestphp/pest` at `v4.7.4` (exact versions verified during planning; a later run may resolve a newer patch, which is fine) — every later task assumes these are installed. **The suite will NOT be green after this task alone** — Task 4 fixes a hard failure that blocks ~all tests; that's expected, verified during planning, and gets fixed next.

- [ ] **Step 1: Edit composer.json version constraints**

In the `require` block, change:

```json
"laravel-notification-channels/telegram": "^5.0",
"laravel/framework": "^10.0",
"laravel/sanctum": "^3",
"laravel/tinker": "^2",
```

to:

```json
"laravel-notification-channels/telegram": "^7.0",
"laravel/framework": "^13.0",
"laravel/sanctum": "^4.0",
"laravel/tinker": "^3.0",
```

And change:

```json
"spatie/laravel-analytics": "^5.6",
"spatie/laravel-settings": "^2.4",
```

to:

```json
"spatie/laravel-analytics": "^5.7",
"spatie/laravel-settings": "^3.0",
```

(`spatie/laravel-activitylog`, `laravel/nightwatch`, `sentry/sentry-laravel`, `laravel/sail`, `spatie/laravel-ignition`, and `laravel/ui` constraints stay unchanged — their existing ranges already cover Laravel 13, only their locked versions need bumping, which the update command in Step 2 handles by naming them explicitly.)

In the `require-dev` block, change:

```json
"nunomaduro/collision": "^7.0",
"pestphp/pest": "^2",
"pestphp/pest-plugin-laravel": "^2",
"phpunit/phpunit": "^10",
```

to:

```json
"nunomaduro/collision": "^8.0",
"pestphp/pest": "^4.0",
"pestphp/pest-plugin-laravel": "^4.0",
"phpunit/phpunit": "^12.0",
```

- [ ] **Step 2: Run the verified composer update**

This exact package list was verified end-to-end during planning (a full trial upgrade was run and reverted). Every package named here either directly constrains `laravel/framework` or has a locked version too old for Laravel 13, even though its composer.json range already permits it — `composer update` only touches packages named on the command line (plus their dependencies via `--with-all-dependencies`), so all of them must be listed together or the solver reports a conflict:

```bash
composer update \
  laravel/framework laravel/sanctum laravel/tinker laravel-notification-channels/telegram \
  spatie/laravel-settings spatie/laravel-analytics laravel/nightwatch spatie/laravel-activitylog \
  sentry/sentry-laravel laravel/sail spatie/laravel-ignition laravel/ui \
  pestphp/pest pestphp/pest-plugin-laravel phpunit/phpunit nunomaduro/collision \
  --with-all-dependencies
```

Expected: ends with `Generating optimized autoload files` / package discovery output, no `Your requirements could not be resolved` error. It prints ~90-130 lines of `Upgrading ...` — that's expected. Verified output ends with `No security vulnerability advisories found.`

- [ ] **Step 3: Verify the key versions landed**

Run: `composer show laravel/framework 2>/dev/null | grep -E "^(name|versions)"` and repeat for `pestphp/pest`, `pestphp/pest-plugin-laravel`, `phpunit/phpunit`, `nunomaduro/collision`.
Expected: `laravel/framework` at `* v13.x.x`, `pestphp/pest` at `* v4.x.x`, `pestphp/pest-plugin-laravel` at `* v4.x.x`, `phpunit/phpunit` at `* 12.x.x`, `nunomaduro/collision` at `* v8.x.x`.

- [ ] **Step 4: Regenerate the autoloader and confirm Composer's own integrity check passes**

Run: `composer validate --no-check-all`
Expected: `./composer.json is valid`

- [ ] **Step 5: Commit, folding in the pre-existing uncommitted analytics config change**

```bash
git add composer.json composer.lock config/analytics.php
git commit -S -m "chore: upgrade laravel/framework to ^13.0, pest to ^4.0, and dependent packages"
```

---

### Task 4: Fix SQLite-incompatible migration teardown (api_token column drop)

**Context:** This is the single highest-impact fix in this plan. Without it, **126 of ~126 test assertions fail** immediately after Task 3 — every test that touches the database fails during teardown, not just tests related to this column. Verified by actually running the suite after Task 3's composer bump during planning.

Laravel 11 removed Doctrine DBAL from schema operations (see Task 2). Previously, Doctrine handled the SQLite-specific table-rebuild needed to drop a column, including implicitly cleaning up any index on that column. Laravel's native (Doctrine-free) SQLite grammar does not do this automatically: dropping a column that still has an index on it fails with `SQLSTATE[HY000]: General error: 1 error in index ... after drop column`.

`tests/TestCase.php` uses the `DatabaseMigrations` trait, which rolls back all migrations (calling every migration's `down()`) after each test. `database/migrations/2020_08_03_181321_adds_api_token_to_users_table.php` has a `down()` that drops the `api_token` column, which has a unique index — so this fires after literally every test that boots the app.

**Files:**
- Modify: `database/migrations/2020_08_03_181321_adds_api_token_to_users_table.php`

**Interfaces:**
- Consumes: nothing from earlier tasks except the Laravel 13 upgrade itself (Task 3) — this bug is triggered purely by the framework version bump.
- Produces: a working test teardown — every subsequent task's test runs depend on this fix.

- [ ] **Step 1: Add the missing dropUnique call**

In `database/migrations/2020_08_03_181321_adds_api_token_to_users_table.php`, change:

```php
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['api_token']);
        });
    }
```

to:

```php
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['api_token']);
            $table->dropColumn(['api_token']);
        });
    }
```

- [ ] **Step 2: Run the full suite to confirm the mass failure is gone**

Run: `vendor/bin/pest`
Expected: failure count drops from ~126 down to a small number (verified during planning: down to exactly 1 failure, in `tests/Feature/Api/SettingsTest.php`, which Task 6 fixes). If you still see the `error in index ... after drop column` message anywhere, search `database/migrations/` for other `dropColumn` calls on indexed/unique columns — none were found during planning (`grep -rl "dropColumn" database/migrations/` lists 6 files; only this one drops a column with a unique index).

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2020_08_03_181321_adds_api_token_to_users_table.php
git commit -S -m "fix: drop the api_token unique index before dropping the column (Laravel 13 native SQLite schema)"
```

---

### Task 5: Publish the Sanctum v4 migration

**Context:** Sanctum v3 auto-loaded its `personal_access_tokens` migration straight from `vendor/laravel/sanctum/database/migrations`. Sanctum v4 stopped doing that, so any environment that runs migrations from scratch (a fresh install, a fresh CI database) needs the migration published into `database/migrations`.

Verified during planning: `php artisan vendor:publish --tag=sanctum-migrations` publishes the file under its **original, unchanged filename** — `2019_12_14_000001_create_personal_access_tokens_table.php` — not a new today-dated name. Production's `migrations` table already has this exact filename recorded (from when Sanctum v3 auto-loaded it originally), so Laravel's migration runner will treat it as already-applied and skip it there; no guard code is needed. This app's test suite doesn't exercise Sanctum token creation directly, so this task has no test to turn green — it's still required because `app/Http/Controllers/Api/UsersController.php` calls `createToken()` in production, and any future fresh install/environment needs the table.

**Files:**
- Create: `database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`
- Verify only (no edit expected): `config/sanctum.php`

**Interfaces:**
- Consumes: `laravel/sanctum` at `^4.0` (Task 3).
- Produces: a `personal_access_tokens` table that gets created on fresh databases and is skipped (not re-run) on databases that already have it recorded under this filename.

- [ ] **Step 1: Publish the Sanctum migration**

Run: `php artisan vendor:publish --tag=sanctum-migrations`
Expected output: `Copying directory [vendor/laravel/sanctum/database/migrations] to [database/migrations]` then `DONE`.

- [ ] **Step 2: Confirm the exact filename**

Run: `ls database/migrations | grep personal_access_tokens`
Expected: `2019_12_14_000001_create_personal_access_tokens_table.php` (the original package filename, not a new timestamp).

- [ ] **Step 3: Verify config/sanctum.php needs no changes**

Run: `cat config/sanctum.php`
Expected: the `middleware` array already has `'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class` and `'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class` — these are already the correct v4 key names pointing at this app's custom middleware. No edit needed; this step is a confirmation, not a change.

- [ ] **Step 4: Run the full suite to confirm no regression**

Run: `vendor/bin/pest`
Expected: same result as the end of Task 4 (the new migration creates the table on the fresh in-memory sqlite test database without conflict; it does not fix or break the one remaining Settings failure — Task 6 does that).

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php
git commit -S -m "fix: publish Sanctum v4 personal_access_tokens migration"
```

---

### Task 6: Fix spatie/laravel-settings v3 — settings table migration

**Context:** `spatie/laravel-settings` v3 uses an `upsert(... on conflict ("group", "name") ...)` query to save settings, which requires a genuine unique index on `('group', 'name')`. The original table only had a plain index on `group` alone. Verified during planning: without this migration, `tests/Feature/Api/SettingsTest.php`'s update test fails with `SQLSTATE[HY000]: General error: 1 ON CONFLICT clause does not match any PRIMARY KEY or UNIQUE constraint`. This app has no custom settings repository, so this migration is the only change v3 requires.

**Files:**
- Create: `database/migrations/2026_07_06_000001_update_settings_table_for_laravel_settings_v3.php`

**Interfaces:**
- Consumes: `spatie/laravel-settings` at `^3.0` (Task 3), the existing `settings` table from `database/migrations/2022_07_26_123205_create_settings_table.php` (columns: `group` string indexed, `name` string, `locked` boolean, `payload` json).
- Produces: `settings` table with `locked` defaulting to `false` and a unique index on `['group', 'name']` instead of a plain index on `group`.

- [ ] **Step 1: Write the migration**

Create `database/migrations/2026_07_06_000001_update_settings_table_for_laravel_settings_v3.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group']);
            $table->boolean('locked')->default(false)->change();
            $table->unique(['group', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['group', 'name']);
            $table->boolean('locked')->change();
            $table->index('group');
        });
    }
};
```

- [ ] **Step 2: Run the settings test to confirm it's fixed**

Run: `vendor/bin/pest tests/Feature/Api/SettingsTest.php`
Expected: `Tests: 3 passed (7 assertions)` (verified exact output during planning).

- [ ] **Step 3: Run the full suite to confirm it's back to the Task 0 baseline**

Run: `vendor/bin/pest`
Expected: `Tests: 1 skipped, 125 passed (225 assertions)` — matching Task 0's baseline exactly. `phpunit.xml` needs no changes to reach this; PHPUnit 12.5.30 accepts the existing 10.5-schema config as-is (verified during planning — no schema deprecation warnings appeared anywhere in the run).

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_07_06_000001_update_settings_table_for_laravel_settings_v3.php
git commit -S -m "fix: update settings table schema for spatie/laravel-settings v3"
```

---

### Task 7: Final verification

**Files:** none (verification only)

**Interfaces:**
- Consumes: everything from Tasks 0–6.

- [ ] **Step 1: Full clean install from the committed lock file**

Run: `rm -rf vendor && composer install --no-interaction`
Expected: completes with no errors, confirming `composer.lock` is self-consistent (not dependent on leftover state from the incremental `composer update` calls in Task 3).

- [ ] **Step 2: Confirm the app boots on Laravel 13 without any skeleton changes**

Run: `php artisan about`
Expected: prints the standard Laravel "about" report (Environment, Cache, Drivers sections etc.) with `Laravel Version` showing `13.x`, no fatal errors.

- [ ] **Step 3: Re-run the full test suite one final time**

Run: `vendor/bin/pest`
Expected: `Tests: 1 skipped, 125 passed (225 assertions)` — identical to Task 0's baseline.

- [ ] **Step 4: Confirm composer.json/composer.lock consistency**

Run: `composer validate --strict`
Expected: `./composer.json is valid` (strict mode also checks composer.lock is up to date with composer.json).

- [ ] **Step 5: Review the full diff before treating the upgrade as done**

Run: `git log --oneline main..HEAD` and `git diff main..HEAD --stat`
Expected: 6 commits (Tasks 1–6, in order — Task 0 is verification-only and produces no commit), touching only: `composer.json`, `composer.lock`, `config/analytics.php`, the 5 CI/deploy workflow files, `database/migrations/2020_08_03_181321_adds_api_token_to_users_table.php`, `database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php` (new), and `database/migrations/2026_07_06_000001_update_settings_table_for_laravel_settings_v3.php` (new).
