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
- **The committed admin build has root-absolute asset paths that don't match the test topology.** `public/admin/index.html` references `/js/chunk-vendors....js`, `/js/app....js`, `/css/app....css` (verified: `grep -o 'src="[^"]*"|href="[^"]*"' public/admin/index.html`) — but those files physically live under `public/admin/js/` and `public/admin/css/`. `resources/easyadmin/vue.config.js` has no `publicPath` set (defaults to `/`). **Confirmed with the user:** production serves `/admin` from a separate subdomain/vhost with `public/admin` as its own document root — root-absolute paths are correct there. `pest-plugin-browser`'s single embedded Laravel server has one document root (this app's `public/`), so visiting `/admin` under test would 404 those assets and the SPA would not boot, as-is. **Fix:** the browser-test pre-build step builds the admin SPA specifically for tests with a corrected public path (`vue-cli-service build --dest ../../public/admin --public-path /admin/`), producing a test-only fixture — this is a deliberate divergence from the production build, not a claim about how production is actually configured, and does not touch the production build/deploy process.
- **Session cookie domain is a second, related risk for the embedded server.** `.env.testing` sets `SESSION_DOMAIN=localhost`, and `phpunit.xml` doesn't override it, so `config('session.domain')` is `'localhost'` during all test runs. `pest-plugin-browser`'s embedded server binds to `127.0.0.1` (confirmed in `pest-plugin-browser`'s `Browsable.php`/`LaravelHttpServer.php` docs). A session cookie scoped to `Domain=localhost` will not be sent by a real browser (Chromium, via Playwright) on requests to host `127.0.0.1` — different host strings, no browser-level relationship — which would silently break authenticated-session persistence across page navigations in browser tests specifically (Feature tests are unaffected; they don't use real browser cookie storage). **Fix:** add `<env name="SESSION_DOMAIN" value="null"/>` to `phpunit.xml` (Laravel's `env()` helper casts the literal string `"null"` to PHP `null`, so `session.domain` becomes unset — cookies apply to the current request host with no domain restriction). Global to all suites; harmless for Feature/Unit tests.
- **Deep-linked SPA routes (e.g. `/admin/content/news/overview`) don't resolve under the embedded server without an explicit fallback.** The admin SPA is client-side routed (Vue Router, history mode); only `public/admin/index.html` and its assets are real files. Laravel's own router has no route matching `/admin/*` — an unmatched request there falls through to `routes/web.php`'s `Route::fallback([PageController::class, 'show'])`, which tries to resolve it as a CMS page slug and renders the public site's 404 view instead of the admin SPA. Production's separate vhost handles this via its own web-server rewrite rules (matching the topology confirmed with the user); the single-document-root test server has no equivalent. **Fix:** a testing-only route (`app()->environment('testing')`-gated, `phpunit.xml` forces `APP_ENV=testing`) added to `routes/web.php`: `Route::get('/admin/{any?}', fn () => response()->file(public_path('admin/index.html')))->where('any', '.*');`, placed ahead of the existing fallback route. Never active outside test runs.
- **Native `confirm()` dialogs gate every delete flow, and `pest-plugin-browser`'s dialog-handling API is not documented anywhere found during research** (3 targeted doc queries against the plugin's own docs came up empty). Every delete action across News, Events, Pages, Persons, and Users (and file deletion in `AttachmentsCard.vue`) is a native `window.confirm(...)` call, not a Vue/CoreUI modal (verified: `News.vue:134`, `Persons.vue:214`, `User.vue:354,377`, `AttachmentsCard.vue:152`). Playwright's default behavior is to auto-dismiss native dialogs (i.e. `confirm()` returns `false`) unless a handler is registered — if `pest-plugin-browser` doesn't surface one, delete tests would silently not delete anything while still "passing" any assertion that doesn't check the item is actually gone. **This is resolved empirically, not by more doc research** — Task 1's walking-skeleton spike includes one create-then-delete round trip specifically to observe and lock in the actual behavior before any other delete test is written.
- **No public self-registration UI exists.** `resources/easyadmin/src/components/` has `Login.vue` but no `Register.vue` — the `POST api/register` route (covered by the existing Feature-level `RegisterTest.php`) has no corresponding SPA screen. Browser e2e auth coverage is therefore login + logout only.
- **CI currently pins Node to 16.x** (`.github/workflows/test.yml`, `test-easyadmin` job). Current Playwright releases require Node 18+; the new browser-test job introduced by this work uses Node 20.x LTS (the existing `test-app`/`test-easyadmin` jobs are untouched — see CI section).
- **Persons and Users are structurally different from News/Events/Pages** and were kept in full scope after re-confirming with the user (see below), at the cost of extra research and more brittle tests: Persons is a drag-and-drop board (`vue-smooth-dnd`) with person creation via a `vue-multiselect` taggable combobox (no separate create route); Users is inline table-row editing where new-row inputs have no `name`/`label`/`aria-label` (only `v-model`), so selectors fall back to table position (`tr:nth-child(n) td:nth-child(n) input`) — the most fragile tests in this suite.

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
- `RefreshDatabase` is already applied globally in `tests/Pest.php` (line 15, unscoped) — no new binding needed for it. Add `uses(Tests\TestCase::class)->in('Browser');` alongside the existing `.in('Unit')`/`.in('Feature')` lines, so `tests/Browser` gets the same app `TestCase` (migrations, `db:seed`, cache clearing) as the other suites.
- CRUD/upload tests start pre-authenticated via `actingAs()`/`authenticate()` — only `AuthTest.php` drives the real login form. This keeps a login-form regression from failing 30+ unrelated tests, and matches the plugin's built-in support for cookie-based pre-authentication.
- Assertions favor `assertSee()` / `assertPath()` / `assertRoute()` / `assertNoBrokenImages()` over screenshot-diffing.

### Build freshness

Both frontends must be built from current source before running browser tests, not trusted from whatever's committed (which can silently drift from the code under test). The admin SPA build uses a corrected `--public-path` for the reasons covered under Confirmed Facts:

```bash
npm run production                                                          # root — Laravel Mix, public site assets
npm --prefix resources/easyadmin run build -- --dest ../../public/admin --public-path /admin/   # admin SPA, test-only public path fix
vendor/bin/pest tests/Browser
```

This becomes a documented local pre-test step and a CI step (see below). It permanently overwrites the committed `public/admin` with a test-specific build — acceptable for a local/CI test run, but this build must never be committed back (the plan adds a `.gitignore`-friendly check, not a rebuild-and-commit step).

### CI

A **separate** GitHub Actions job (not folded into the existing `test-app` job), so a browser-test flake doesn't block the fast Feature/Unit signal, while both must still pass for the PR to go green. The job:
1. Checks out, sets up PHP 8.3 and Node 20.x (bumped from 16.x).
2. Builds both frontends (`npm run production`; `resources/easyadmin`'s corrected-public-path build above).
3. Installs `pestphp/pest-plugin-browser` + Playwright + Chromium.
4. Runs against a MySQL service (matching `test-app`'s existing setup — Sanctum session auth and a couple of existing settings/upsert behaviors are MySQL-specific).
5. Runs `vendor/bin/pest tests/Browser`.

Node 20.x bump applies to this new job; whether to also bump the existing `test-app`/`test-easyadmin` jobs off 16.x is a candidate follow-up, not required by this work (they don't use Playwright).

## Testing

This design doc **is** the test plan — the deliverable itself is the test suite described above. No additional test-the-tests layer.

The implementation plan front-loads the three empirical unknowns listed under Confirmed Facts (SPA boot under the corrected public path, session cookie persistence across navigations, native `confirm()` dialog behavior) into one walking-skeleton task before any of the seven test files are written in full. This is deliberate: those three are facts about this app under this specific server that no amount of documentation research resolves, and every later task depends on all three holding.

## Success criteria

- `vendor/bin/pest tests/Browser` passes green locally against freshly built frontends.
- Every flow listed under Scope has at least one passing browser test.
- New CI job runs on PRs and passes.
- Existing `tests/Feature` and `tests/Unit` suites are unaffected (still `1 skipped, 133 passed`).
