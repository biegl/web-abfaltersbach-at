# Pest Browser E2E Tests — Design

**Date:** 2026-07-06
**Status:** Approved

## Goal

Add real-browser end-to-end test coverage using `pestphp/pest-plugin-browser`, covering the public site, admin login/logout, and full CRUD (including file upload) for all five admin entities managed through the Vue SPA at `/admin`.

This is Phase 3 of the e2e test harness work outlined in `docs/superpowers/specs/2026-07-06-e2e-test-harness-design.md` (Phase 1: Feature/API route coverage, already merged; Phase 2: the Laravel 10→13 / Pest 2→4 upgrade in `task/laravel-13-pest-4-upgrade`, PR #2141). Phase 2 was the blocker — `pest-plugin-browser` requires PHP 8.3+ and Pest 4.4.5+, both now satisfied.

## Why there's nothing to "convert"

The request that kicked this off assumed existing Laravel Dusk tests to migrate. There are none: no `laravel/dusk` in `composer.json`, no `tests/Browser` directory, no Dusk references anywhere in the repo (verified via repo-wide grep). This is net-new browser test coverage, not a migration.

## Confirmed facts (verified during design, not assumed)

- **`pest-plugin-browser` boots an embedded, in-process HTTP server per test** (an embedded AMP server, not a `php artisan serve` subprocess like Dusk uses). Because it's in-process, it shares the same DB connection/app state as the test itself — `RefreshDatabase` and `actingAs()`/`authenticate()` work exactly like standard Feature tests (cookies from `prepareCookiesForRequest()` are merged into browser requests). No Dusk-style separate-process DB sync workaround is needed.
- **Requirements:** PHP 8.3+, Pest 4.4.5+ (this repo has PHP `^8.3` and `pestphp/pest` `v4.7.4` — both satisfied), Node.js, `@playwright/test`.
- **The admin SPA build (`public/admin`) is already committed to git** (built via `resources/easyadmin`'s `npm run build`, which copies `dist/` → `../../public/admin` via a `postbuild` script). It is not built fresh in the main `test-app` CI job today.
- **No public self-registration UI exists.** `resources/easyadmin/src/components/` has `Login.vue` but no `Register.vue` — the `POST api/register` route (covered by the existing Feature-level `RegisterTest.php`) has no corresponding SPA screen. Browser e2e auth coverage is therefore login + logout only.
- **CI currently pins Node to 16.x** (`.github/workflows/test.yml`, `test-easyadmin` job). Current Playwright releases require Node 18+; the new browser-test job introduced by this work uses Node 20.x LTS (the existing `test-app`/`test-easyadmin` jobs are untouched — see CI section).

## Scope

**In scope:**
- Public site: home page rendering (including the event list's month/day names — a regression guard for the Carbon 3 `translatedFormat()` fix landed in the prior branch), navigation via links, unknown-path 404 view, file download link.
- Admin auth: login via the real `Login.vue` form (success → dashboard, bad credentials → visible error), logout → redirected to login.
- Admin CRUD via the SPA, full create/edit/delete/list for all five entities: News, Events, Pages, Persons, Users. Each flow navigates via the SPA sidebar at least once (not only direct URL visits), to catch real navigation regressions.
- File upload via the UI: attaching a file to a News item through the Uppy widget (one flow, not a generic separate test — News is where file attachment is most central to the entity's real usage).
- Users CRUD additionally asserts a non-admin session cannot reach `/admin/users`.

**Out of scope (explicitly deferred):**
- Firefox/WebKit — Chromium only.
- Accessibility assertions (`axe-core`, which the plugin supports) — separate follow-up if wanted.
- Visual regression / screenshot diffing.
- Dusk removal — moot, none exists.

## Architecture

### Branch

New branch `tests/e2e-phase3-browser`, based on `task/laravel-13-pest-4-upgrade` directly (not `main`) — this work depends on the Laravel 13/Pest 4 upgrade in that branch/PR, and there's no reason to wait for it to merge first.

### Dependencies

```bash
composer require --dev pestphp/pest-plugin-browser
npm install --save-dev @playwright/test
npx playwright install --with-deps chromium
```

### Test organization

```
tests/
  Browser/
    PublicSiteTest.php
    AuthTest.php
    Admin/
      NewsCrudTest.php      # includes the file-upload-via-Uppy flow
      EventsCrudTest.php
      PagesCrudTest.php
      PersonsCrudTest.php
      UsersCrudTest.php
```

Mirrors `tests/Feature/Api/*` naming so the API-level (Phase 1) and browser-level (Phase 3) suites read as companions per entity.

### Conventions & helpers

- Reuse Phase 1's `asAdmin()` helper and factories (`Person`, `Module`, etc.) from `tests/Pest.php`.
- `pest()->uses(RefreshDatabase::class)` scoped to `tests/Browser` (matching the plugin's documented pattern).
- CRUD/upload tests start pre-authenticated via `actingAs()`/`authenticate()` — only `AuthTest.php` drives the real login form. This keeps a login-form regression from failing 30+ unrelated tests, and matches the plugin's built-in support for cookie-based pre-authentication.
- Assertions favor `assertSee()` / `assertPath()` / `assertRoute()` / `assertNoBrokenImages()` over screenshot-diffing.

### Build freshness

Both frontends must be built from current source before running browser tests, not trusted from whatever's committed (which can silently drift from the code under test):

```bash
npm run production                              # root — Laravel Mix, public site assets
npm --prefix resources/easyadmin run build       # admin SPA → public/admin
vendor/bin/pest tests/Browser
```

This becomes a documented local pre-test step and a CI step (see below).

### CI

A **separate** GitHub Actions job (not folded into the existing `test-app` job), so a browser-test flake doesn't block the fast Feature/Unit signal, while both must still pass for the PR to go green. The job:
1. Checks out, sets up PHP 8.3 and Node 20.x (bumped from 16.x).
2. Builds both frontends (`npm run production`, `resources/easyadmin`'s `npm run build`).
3. Installs `pestphp/pest-plugin-browser` + Playwright + Chromium.
4. Runs against a MySQL service (matching `test-app`'s existing setup — Sanctum session auth and a couple of existing settings/upsert behaviors are MySQL-specific).
5. Runs `vendor/bin/pest tests/Browser`.

Node 20.x bump applies to this new job; whether to also bump the existing `test-app`/`test-easyadmin` jobs off 16.x is a candidate follow-up, not required by this work (they don't use Playwright).

## Testing

This design doc **is** the test plan — the deliverable itself is the test suite described above. No additional test-the-tests layer.

## Success criteria

- `vendor/bin/pest tests/Browser` passes green locally against freshly built frontends.
- Every flow listed under Scope has at least one passing browser test.
- New CI job runs on PRs and passes.
- Existing `tests/Feature` and `tests/Unit` suites are unaffected (still `1 skipped, 133 passed`).
