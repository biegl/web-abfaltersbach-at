# Pest Browser E2E Tests Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add real-browser e2e coverage (`pestphp/pest-plugin-browser`) for the public site, admin login/logout, and full CRUD (incl. file upload) across all five admin SPA entities.

**Architecture:** `pest-plugin-browser` boots an embedded, in-process Laravel HTTP server per test and drives it via Playwright/Chromium — no separate process, `RefreshDatabase`/`actingAs()` work like normal Feature tests. The admin Vue SPA (built separately under `resources/easyadmin`, output copied to `public/admin`) needs a test-only rebuild with a corrected `--public-path` to boot under this single-document-root server; this never touches the production build. Tests live under `tests/Browser/`, mirroring `tests/Feature/Api/`'s per-entity naming.

**Tech Stack:** PHP 8.3, Laravel 13, Pest 4 + `pest-plugin-browser`, Playwright (Chromium only), Vue 2 / CoreUI / vue-multiselect / vue-smooth-dnd (admin SPA, unchanged).

## Global Constraints

- Chromium only — no Firefox/WebKit.
- **[Corrected during Task 1 — see `.superpowers/sdd/task-1-report.md` Finding 3]** `asAdmin()`/`asUser()` (session-cookie auth via `actingAs()`) do **not** satisfy the admin SPA's client-side router guard — it gates on `sessionStorage['user']`, set only by a real login POST response, which `pest-plugin-browser`'s cookie injection never touches. Every CRUD test that needs to land on a protected `/admin/*` page must use the shared helpers added to `tests/Pest.php`: `visitAsAdmin(string $path)` / `visitAsUser(string $path)` (create the user, log in via the real form, `navigate()` to `$path`, return the `Webpage`). `asAdmin()`/`asUser()` are still fine for server-side-only setup (e.g. attaching ownership to a factory record) that doesn't depend on the SPA's own auth state. Once logged in, use `->navigate()` for same-tab transitions, never a fresh `visit()` (which opens a new context and drops `sessionStorage`).
- Full CRUD (list/create/edit/delete) for all five entities: News, Events, Pages, Persons, Users.
- File upload covered once, on News, via the Uppy widget.
- No screenshot-diffing, no accessibility assertions, no Firefox/WebKit — explicitly out of scope per spec.
- Full reference: `docs/superpowers/specs/2026-07-06-pest-browser-e2e-design.md`.
- Never commit a test-rebuilt `public/admin` — it must always be restored to the production build (`git checkout -- public/admin && git clean -fd public/admin/`) after local test runs. Each task's steps that rebuild it end with this restore.
- **[Corrected during Task 1 — Finding 1]** The admin SPA build command is now `VUE_APP_PUBLIC_PATH=/admin/ VUE_APP_API_HOST= npm --prefix resources/easyadmin run build` (no `--dest`, no `--public-path` — neither is a real `@vue/cli-service` 5.x flag; `publicPath`/`Config.host` now read these env vars, added in Task 1's commit). Every later task's "rebuild the admin SPA" step means this command, not the plan's original literal text.
- **[Corrected during Task 1 — Finding 5]** Because `publicPath` is `/admin/` under test, the Vue Router `base` is too — every SPA-internal route (redirects, logout target) is `/admin/login`, `/admin/dashboard`, etc., not bare `/login`.
- Dialog handling (every delete test): call `->script('window.confirm = () => true;')` right after reaching the target page, before the delete click — `pest-plugin-browser` v4.3.1 has no dialog API; see `tests/Browser/AuthTest.php`'s header comment.

---

### Task 1: Infrastructure, dependencies, and the auth + dialog-handling spike

**Context:** This task resolves three empirical unknowns before any other test is written (see spec's Confirmed Facts): does the rebuilt admin SPA boot under the embedded server, does the session cookie survive navigation (the `SESSION_DOMAIN=localhost` vs `127.0.0.1` mismatch), and how does `pest-plugin-browser` handle the native `confirm()` dialogs every delete flow uses. All three get proven here via `AuthTest.php` (which needs SPA-boot + session-persistence to pass at all) plus one isolated delete test (which needs dialog-handling).

**Files:**
- Modify: `composer.json`, `composer.lock` (via `composer require`)
- Modify: `package.json`, `package-lock.json` (via `npm install`)
- Modify: `phpunit.xml` (add `SESSION_DOMAIN` env override)
- Modify: `tests/Pest.php` (bind `Tests\TestCase` to `Browser`)
- Modify: `routes/web.php` (add a testing-only catch-all so deep-linked SPA routes resolve to the admin `index.html`)
- Create: `tests/Browser/AuthTest.php`

**Interfaces:**
- Produces: a working `tests/Browser` suite scaffold and the confirmed dialog-handling pattern — every later CRUD task's delete step copies whatever pattern Step 8 below discovers.

- [ ] **Step 1: Install pest-plugin-browser and Playwright**

```bash
composer require --dev pestphp/pest-plugin-browser
npm install --save-dev @playwright/test
npx playwright install --with-deps chromium
```

Expected: `composer.json`'s `require-dev` gains `"pestphp/pest-plugin-browser"`; `package.json`'s `devDependencies` gains `"@playwright/test"`; Playwright reports Chromium installed (or already present).

- [ ] **Step 2: Fix session cookie domain for the embedded server**

In `phpunit.xml`, inside the existing `<php>` block (alongside the other `<env>` tags), add:

```xml
<env name="SESSION_DOMAIN" value="null"/>
```

Why: `.env.testing` sets `SESSION_DOMAIN=localhost`; the embedded server binds to `127.0.0.1`. A cookie scoped to `Domain=localhost` is not sent by a real browser on requests to `127.0.0.1`. Laravel's `env()` helper casts the literal string `"null"` to PHP `null`, which makes `config('session.domain')` unset (no domain restriction).

- [ ] **Step 3: Bind the app TestCase to the Browser directory**

In `tests/Pest.php`, change:

```php
uses(Tests\TestCase::class)->in('Unit');
uses(Tests\TestCase::class)->in('Feature');
```

to:

```php
uses(Tests\TestCase::class)->in('Unit');
uses(Tests\TestCase::class)->in('Feature');
uses(Tests\TestCase::class)->in('Browser');
```

(`RefreshDatabase` on line 15 is already unscoped/global — no change needed there.)

- [ ] **Step 4: Build both frontends for testing**

```bash
npm run production
npm --prefix resources/easyadmin run build -- --dest ../../public/admin --public-path /admin/
```

Expected: both complete without errors. `git status` will now show `public/admin/*` as modified (different asset hashes/public-path than the committed production build) — this is expected for the duration of this testing session; Step 10 restores it.

- [ ] **Step 5: Add a testing-only route so deep-linked SPA paths resolve to the admin SPA**

**Why:** the admin SPA is a client-side-routed (Vue Router, history mode) app. `public/admin/index.html` and its assets are real static files, served directly without touching Laravel's router. But a URL like `/admin/content/news/overview` is a route Vue Router understands, not a real file — Laravel's own router has no route matching `/admin/*`, so an unmatched request there falls through to `routes/web.php:20`'s `Route::fallback([PageController::class, 'show'])`, which tries to resolve it as a CMS page slug and renders the public site's 404 view instead of the admin SPA. In real production this is handled by the separate vhost's own web-server rewrite rules (confirmed with the user — see spec); this embedded single-document-root test server has no equivalent, so every deep `visit('/admin/...')` call in this plan (including this same task's own login test, and every later CRUD task) would fail without this route.

In `routes/web.php`, add (before the existing `Route::fallback(...)` line, so it takes effect for anything under `/admin` that isn't a literal static file):

```php
if (app()->environment('testing')) {
    Route::get('/admin/{any?}', function () {
        return response()->file(public_path('admin/index.html'));
    })->where('any', '.*');
}
```

`app()->environment('testing')` is reliably true during every `vendor/bin/pest` run — `phpunit.xml` forces `APP_ENV=testing` — and false in any real environment, so this never affects production routing. Place this above `Route::fallback(...)` (Laravel matches non-fallback routes first regardless of order relative to `fallback()`, but keeping it visually above the line it's designed to pre-empt makes the intent obvious to a future reader).

- [ ] **Step 6: Investigate pest-plugin-browser's dialog-handling API**

```bash
grep -rn "dialog\|Dialog" vendor/pestphp/pest-plugin-browser/src/
```

Read any matching file's surrounding context. Look specifically for a method that accepts/dismisses native browser dialogs (`confirm()`/`alert()`), or for evidence the plugin's Playwright wrapper auto-accepts dialogs by default (check `src/Client.php` or similar connection-setup code for `page.on('dialog', ...)` equivalents). Record what you find — Step 8 depends on it. If no explicit API exists and default Playwright behavior applies (auto-dismiss, i.e. `confirm()` returns `false`), Step 8's test will fail cleanly and you'll see exactly that (the item stays in the list) — that failure is itself the answer, not a bug to work around blindly. If it fails this way, check the installed plugin's version changelog/README (`vendor/pestphp/pest-plugin-browser/README.md` or CHANGELOG) for a documented option; if genuinely nothing exists, report BLOCKED with what you found — this is a real gap the controller needs to decide how to handle (e.g. pinning a plugin version that does support it, or filing an upstream issue) before any delete test can be written.

- [ ] **Step 7: Write the auth spike tests**

Create `tests/Browser/AuthTest.php`:

```php
<?php

use App\Models\Role;
use App\Models\User;

it('redirects a guest to the login page', function () {
    visit('/admin')
        ->assertPathBeginsWith('/login');
});

it('logs in via the form and reaches the dashboard', function () {
    $user = User::factory()->create(['role' => Role::ADMIN]);

    visit('/admin/login')
        ->type('username', $user->email)
        ->type('password', 'password')
        ->click('Anmelden')
        ->assertPathIs('/admin/dashboard')
        ->assertSee('News');
});

it('shows an error on invalid credentials', function () {
    User::factory()->create(['role' => Role::ADMIN, 'email' => 'admin@example.com']);

    visit('/admin/login')
        ->type('username', 'admin@example.com')
        ->type('password', 'wrong-password')
        ->click('Anmelden')
        ->assertSee('Benutzername und/oder Passwort falsch!');
});

it('logs out and returns to the login page', function () {
    asAdmin(); // existing tests/Pest.php helper — calls test()->actingAs($user) internally

    visit('/admin/dashboard')
        ->assertSee('News')
        ->visit('/admin/logout')
        ->assertPathBeginsWith('/login');
});
```

Note: `type('username', ...)` targets `Login.vue`'s `input[name="username"]` — the value is the user's real **email** (the form posts it as `email` to the backend despite the field's local name; verified in `resources/easyadmin/src/services/auth.service.ts`). `User::factory()`'s default password is `'password'` (verified in `database/factories/UserFactory.php` via `Hash::make('password')`).

The fourth test uses the existing `asAdmin()` global helper from `tests/Pest.php` (already used throughout `tests/Feature`, e.g. `beforeEach(fn () => asUser())` in `tests/Feature/Api/NewsTest.php`) rather than constructing a `User` and calling `actingAs()` manually — `asAdmin()` already does `test()->actingAs($user)` internally, and the spec's Confirmed Facts confirm `pest-plugin-browser` picks up any `actingAs()` call made before `visit()` automatically via `prepareCookiesForRequest()`. No chained authentication call on the `Webpage` object is needed or exists. This is the pattern every later CRUD task uses — call `asAdmin()`/`asUser()` before `visit()`, never chained after it.

- [ ] **Step 8: Run the auth spike and fix forward until green**

Run: `vendor/bin/pest tests/Browser/AuthTest.php`

If the first test (guest redirect) fails with a 404 or blank page instead of a redirect, the admin SPA isn't booting — check the browser console/network errors (`assertNoJavaScriptErrors()` can help pinpoint this) and re-verify Step 4's build actually produced `/admin/js/...`-referencing HTML: `grep -o 'src="[^"]*"' public/admin/index.html` should now show `/admin/js/...`, not `/js/...`.

If the second test fails specifically on `assertSee('News')` after apparently reaching `/admin/dashboard` (i.e., login succeeded but the dashboard's own authenticated API calls come back as 401), this is the session-cookie-domain issue from Step 2 not having taken effect — double check `phpunit.xml`'s `<env>` block and re-run.

Expected once fixed: `Tests: 4 passed`.

- [ ] **Step 9: Write and resolve the delete-confirmation spike**

Add to `tests/Browser/AuthTest.php` (or a separate `tests/Browser/DialogHandlingSpikeTest.php` if you prefer — either is fine, this is a one-off proof, not permanent structural test organization):

```php
use App\Models\News;

it('can delete an existing news item, proving native confirm() dialog handling', function () {
    asAdmin();
    $news = News::factory()->create(['title' => 'Löschen mich']);

    $page = visit('/admin/content/news/overview')
        ->assertSee('Löschen mich');

    // Apply whatever dialog-handling mechanism Step 6 found here before/around this click.
    $page->click('[aria-label="Löschen"]')
        ->assertDontSee('Löschen mich');
});
```

Run it. Iterate using whatever Step 6 found (e.g. if the plugin exposes an `acceptDialog()`-style method, chain it before or after `click()` per its documented usage; if dialogs are auto-accepted by default, this test passes with no extra code and that fact becomes your answer for every later delete test). Once green, write one sentence at the top of this test file as a comment recording the exact mechanism that worked, e.g. `// Native confirm() dialogs are auto-accepted by default by this plugin version — no extra handling needed.` — every later task's delete steps reference this comment instead of re-deriving it.

- [ ] **Step 10: Run the full Browser suite**

Run: `vendor/bin/pest tests/Browser`
Expected: `Tests: 5 passed` (4 from Step 7 + 1 from Step 9).

- [ ] **Step 11: Restore the production admin build and commit**

```bash
git checkout -- public/admin
git status
```

Expected: `public/admin` shows no changes (back to the committed production build); only `composer.json`, `composer.lock`, `package.json`, `package-lock.json`, `phpunit.xml`, `tests/Pest.php`, `routes/web.php`, `tests/Browser/AuthTest.php` (and its spike test) are staged.

```bash
git add composer.json composer.lock package.json package-lock.json phpunit.xml tests/Pest.php routes/web.php tests/Browser/AuthTest.php
git commit -S -m "test: add pest-plugin-browser infra, auth tests, and dialog-handling spike"
```

---

### Task 2: PublicSiteTest.php

**Files:**
- Create: `tests/Browser/PublicSiteTest.php`

**Interfaces:**
- Consumes: nothing from Task 1 beyond the working infra (this test doesn't touch `/admin` at all — it's the blade-rendered public site, unaffected by the admin SPA build-path issue).

- [ ] **Step 1: Write the tests**

Create `tests/Browser/PublicSiteTest.php`:

```php
<?php

use App\Models\Event;
use App\Models\Navigation;
use App\Models\Page;
use Carbon\Carbon;

it('renders the home page with the event list', function () {
    $page = Page::factory()->create();
    Navigation::factory()->create(['refID' => $page->id, 'linkname' => 'startseite']);
    Event::factory()->create([
        'date' => Carbon::today(),
        'text' => 'Ein Testereignis',
    ]);

    visit('/')
        ->assertSee(Carbon::today()->translatedFormat('F'))
        ->assertNoBrokenImages();
});

it('navigates to a page via its navigation slug', function () {
    $page = Page::factory()->create(['inhalt' => 'Einzigartiger Seiteninhalt']);
    Navigation::factory()->create(['refID' => $page->id, 'linkname' => 'ueber-uns']);

    visit('/ueber-uns')
        ->assertSee('Einzigartiger Seiteninhalt');
});

it('renders the 404 view for an unknown path', function () {
    visit('/this-path-does-not-exist')
        ->assertSee('404');
});

it('downloads a file via its public link', function () {
    $file = \App\Models\File::factory()->create();

    visit('/files/'.$file->file)
        ->assertNoJavaScriptErrors();
});
```

Check `database/factories/NavigationFactory.php` and `database/factories/FileFactory.php` for the exact fillable field names if `refID`/`linkname`/`file` don't match — these are carried over from the Phase 1 Feature test harness's factories (`tests/Feature/Web/PageTest.php` and `tests/Feature/Web/FileDownloadTest.php` use the same factories; check those files for the exact field names and 404-view text if `assertSee('404')` doesn't match the actual rendered text).

- [ ] **Step 2: Run and fix forward**

Run: `vendor/bin/pest tests/Browser/PublicSiteTest.php`
Expected: `Tests: 4 passed`. Adjust factory field names and the 404 assertion text based on actual failures — cross-reference `tests/Feature/Web/PageTest.php` and `FileDownloadTest.php` for known-correct values.

- [ ] **Step 3: Run full Browser suite and commit**

Run: `vendor/bin/pest tests/Browser`
Expected: `Tests: 9 passed` (5 from Task 1 + 4 here).

```bash
git add tests/Browser/PublicSiteTest.php
git commit -S -m "test: add public site browser tests"
```

---

### Task 3: NewsCrudTest.php (full CRUD + file upload)

**Context:** This is the pattern-setting task — News uses the most common admin form pattern (`ListEntryItem` list, `CInput` + `TextEditor` form) and is where the file-upload flow lives.

**Files:**
- Create: `tests/Browser/Admin/NewsCrudTest.php`

**Interfaces:**
- Consumes: the dialog-handling comment/pattern recorded in Task 1's `AuthTest.php`.
- Produces: the CRUD pattern Tasks 4 (Events) and 5 (Pages) copy directly (same `ListEntryItem`/`TextEditor` structure).

- [ ] **Step 1: Rebuild the admin SPA for this test run**

```bash
VUE_APP_PUBLIC_PATH=/admin/ VUE_APP_API_HOST= npm --prefix resources/easyadmin run build
```

(Required before every task that visits `/admin` — the production build is restored at the end of each such task's steps. **[Corrected during Task 1]** command updated — see Global Constraints.)

- [ ] **Step 2: Write the test file**

Create `tests/Browser/Admin/NewsCrudTest.php`. Note the `TextEditor` body field has no distinguishing selector from its label (the component hardcodes `<label>Inhalt</label>` regardless of any `label` prop passed to it — verified in `resources/easyadmin/src/components/TextEditor.vue`), so target the `#source-switch` checkbox to reveal a plain `<textarea>`, then the sole `textarea.form-control` on the page. **[Corrected during Task 1]** each test logs in via `visitAsAdmin()` (see Global Constraints) instead of `beforeEach(fn () => asAdmin())` + `visit()`; the delete test calls `->script('window.confirm = () => true;')` before the delete click (no dialog API exists in `pest-plugin-browser` — see Global Constraints):

```php
<?php

use App\Models\News;

it('lists existing news items', function () {
    News::factory()->create(['title' => 'Erste Meldung']);

    visitAsAdmin('/admin/content/news/overview')
        ->assertSee('Erste Meldung');
});

it('creates a news item via the SPA', function () {
    visitAsAdmin('/admin/content/news/overview')
        ->click('Erstellen')
        ->assertPathIs('/admin/content/news/add')
        ->click('#source-switch')
        ->type('textarea.form-control', 'Neuer Meldungstext')
        ->fill('Titel', 'Brandneue Meldung')
        ->click('Speichern')
        ->assertPathIs('/admin/content/news/overview')
        ->assertSee('Brandneue Meldung');
});

it('edits an existing news item', function () {
    $news = News::factory()->create(['title' => 'Alter Titel']);

    visitAsAdmin('/admin/content/news/overview')
        ->click('.fa-edit')
        ->assertPathIs('/admin/content/news/'.$news->id)
        ->fill('Titel', 'Aktualisierter Titel')
        ->click('Speichern')
        ->assertPathIs('/admin/content/news/overview')
        ->assertSee('Aktualisierter Titel')
        ->assertDontSee('Alter Titel');
});

it('deletes a news item', function () {
    News::factory()->create(['title' => 'Zu löschende Meldung']);

    $page = visitAsAdmin('/admin/content/news/overview')
        ->assertSee('Zu löschende Meldung');

    $page->script('window.confirm = () => true;');

    $page->click('[aria-label="Löschen"]')
        ->assertDontSee('Zu löschende Meldung');
});

it('attaches a file to an existing news item via the Uppy widget', function () {
    $news = News::factory()->create();
    $filePath = base_path('tests/Browser/fixtures/sample.jpg');

    visitAsAdmin('/admin/content/news/'.$news->id)
        ->attach('.uppy-FileInput-input', $filePath)
        ->assertSee('Upload erfolgreich');
});
```

Create the fixture file the upload test needs:

```bash
mkdir -p tests/Browser/fixtures
```

Then create a minimal valid JPEG at `tests/Browser/fixtures/sample.jpg` — the simplest reliable way is copying any existing tiny fixture already in the repo (check `tests/Feature` for `UploadedFile::fake()->image(...)` usage — if only fakes are used and no real file fixture exists, generate one: `php -r "imagejpeg(imagecreatetruecolor(2,2), 'tests/Browser/fixtures/sample.jpg');"` (requires the GD extension, already present per `.github/workflows/test.yml`'s PHP setup) — this produces a real, tiny, valid JPEG Playwright can attach.

- [ ] **Step 3: Run and fix forward**

Run: `vendor/bin/pest tests/Browser/Admin/NewsCrudTest.php`
Expected: `Tests: 5 passed`.

Common fix points, based on verified-but-unexercised assumptions in Step 2:
- If `fill('Titel', ...)` doesn't match, `CInput`'s label/input association may need a different selector strategy — check the rendered `id`/`for` pair by inspecting `NewsEntryForm.vue`'s compiled output, or fall back to a CSS selector targeting the input adjacent to the "Titel" label.
- If the create test's redirect assertion fails, re-check `NewsEntryForm.vue`'s actual post-save behavior (`$router.push('/content/news/overview')` was confirmed during design research, but confirm the exact route name/path still matches).
- If the delete test's dialog doesn't resolve automatically, apply the exact pattern documented in Task 1's `AuthTest.php` comment.

- [ ] **Step 4: Restore the production admin build and commit**

```bash
git checkout -- public/admin
git add tests/Browser/Admin/NewsCrudTest.php tests/Browser/fixtures/sample.jpg
git commit -S -m "test: add News CRUD and file upload browser tests"
```

---

### Task 4: EventsCrudTest.php

**Files:**
- Create: `tests/Browser/Admin/EventsCrudTest.php`

**Interfaces:**
- Consumes: the exact CRUD pattern from Task 3 (`ListEntryItem` list with `.fa-edit`/`[aria-label="Löschen"]`, `PageHeader`'s "Erstellen" button, `TextEditor`'s `#source-switch` trick) — Events uses this identically (verified: `Events.vue` imports and uses `ListEntryItem` and `PageHeader` exactly like `News.vue`).

- [ ] **Step 1: Rebuild the admin SPA, write the test, run, fix forward, restore, commit**

Rebuild (same command as Task 3 Step 1 — the corrected env-var form). Create `tests/Browser/Admin/EventsCrudTest.php`. **[Corrected during Task 1]** `visitAsAdmin()` replaces `beforeEach(fn () => asAdmin())` + `visit()`; delete test uses `->script('window.confirm = () => true;')`:

```php
<?php

use App\Models\Event;

it('lists existing events', function () {
    Event::factory()->create(['text' => 'Sommerfest']);

    visitAsAdmin('/admin/content/events/overview')
        ->assertSee('Sommerfest');
});

it('creates an event via the SPA', function () {
    // Event.date defaults to today client-side (resources/easyadmin/src/models/event.ts) —
    // no DatePicker interaction needed for a happy-path create.
    visitAsAdmin('/admin/content/events/overview')
        ->click('Erstellen')
        ->assertPathIs('/admin/content/events/add')
        ->click('#source-switch')
        ->type('textarea.form-control', 'Neue Veranstaltung')
        ->click('Speichern')
        ->assertPathIs('/admin/content/events/overview')
        ->assertSee('Neue Veranstaltung');
});

it('edits an existing event', function () {
    $event = Event::factory()->create(['text' => 'Altes Fest']);

    visitAsAdmin('/admin/content/events/overview')
        ->click('.fa-edit')
        ->assertPathIs('/admin/content/events/'.$event->id)
        ->click('#source-switch')
        ->type('textarea.form-control', 'Aktualisiertes Fest')
        ->click('Speichern')
        ->assertPathIs('/admin/content/events/overview')
        ->assertSee('Aktualisiertes Fest');
});

it('deletes an event', function () {
    Event::factory()->create(['text' => 'Zu löschendes Fest']);

    $page = visitAsAdmin('/admin/content/events/overview')
        ->assertSee('Zu löschendes Fest');

    $page->script('window.confirm = () => true;');

    $page->click('[aria-label="Löschen"]')
        ->assertDontSee('Zu löschendes Fest');
});
```

Run: `vendor/bin/pest tests/Browser/Admin/EventsCrudTest.php`. Expected: `Tests: 4 passed`. The edit test's `type('textarea.form-control', ...)` replaces existing text — if the TextEditor's textarea doesn't clear on `type()` (some implementations append), check the plugin's `type()` docs for a clear-first guarantee, or use `->fill()` instead if it behaves differently for textareas in this plugin version.

Restore and commit:

```bash
git checkout -- public/admin
git add tests/Browser/Admin/EventsCrudTest.php
git commit -S -m "test: add Events CRUD browser tests"
```

---

### Task 5: PagesCrudTest.php

**Context:** Pages uses a different list component (`PageNavigationItem.vue`, a nested tree, not `ListEntryItem`) with icon-only edit/delete buttons (no `aria-label`, unlike News/Events).

**Files:**
- Create: `tests/Browser/Admin/PagesCrudTest.php`

**Interfaces:**
- Consumes: Task 1's dialog-handling pattern. Does NOT reuse Task 3/4's `.fa-edit`/`[aria-label="Löschen"]` selectors — Pages' edit/delete icons are CoreUI `<CIcon name="cil-pencil">`/`<CIcon name="cil-trash">` with no reliable text/aria selector confirmed during design research.

- [ ] **Step 1: Confirm the actual rendered selector for Pages' edit/delete icons**

Before writing assertions that depend on it, inspect the compiled icon markup: CoreUI's `CIcon` typically renders an `<svg>` or `<i>` with a class derived from its `name` prop (e.g. `.cil-pencil` / `.cil-trash` won't literally exist as CSS classes — CoreUI icons render as inline SVGs referencing a sprite). Run:

```bash
grep -n "cil-pencil\|cil-trash" resources/easyadmin/src/components/PageNavigationItem.vue
```

Read the surrounding template code to find any wrapping element (a `<button>` or `<a>`) with a stable class or `title`/`v-c-tooltip` attribute you can target instead of the icon itself — click the wrapping clickable element, not the icon.

- [ ] **Step 2: Rebuild, write the test**

Rebuild the admin SPA (Task 3 Step 1's command — the corrected env-var form). Create `tests/Browser/Admin/PagesCrudTest.php`. **[Corrected during Task 1]** `visitAsAdmin()` replaces `beforeEach(fn () => asAdmin())` + `visit()`; delete test uses `->script('window.confirm = () => true;')`:

```php
<?php

use App\Models\Page;

it('lists existing pages', function () {
    Page::factory()->create(['seitentitel' => 'Impressum']);

    visitAsAdmin('/admin/content/pages/overview')
        ->assertSee('Impressum');
});

it('creates a page via the SPA', function () {
    visitAsAdmin('/admin/content/pages/overview')
        ->click('Erstellen')
        ->assertPathIs('/admin/content/pages/add')
        ->fill('Titel', 'Neue Seite')
        ->click('#source-switch')
        ->type('textarea.form-control', 'Neuer Seiteninhalt')
        ->click('Speichern')
        ->assertPathIs('/admin/content/pages/overview')
        ->assertSee('Neue Seite');
});

it('edits an existing page', function () {
    $page = Page::factory()->create(['seitentitel' => 'Alte Seite']);

    visitAsAdmin('/admin/content/pages/overview')
        // Use the selector confirmed in Step 1 in place of a placeholder click target:
        ->click('PLACEHOLDER_EDIT_SELECTOR_FROM_STEP_1')
        ->assertPathIs('/admin/content/pages/'.$page->id)
        ->fill('Titel', 'Aktualisierte Seite')
        ->click('Speichern')
        ->assertPathIs('/admin/content/pages/overview')
        ->assertSee('Aktualisierte Seite');
});

it('deletes a page', function () {
    Page::factory()->create(['seitentitel' => 'Zu löschende Seite']);

    $webpage = visitAsAdmin('/admin/content/pages/overview')
        ->assertSee('Zu löschende Seite');

    $webpage->script('window.confirm = () => true;');

    $webpage->click('PLACEHOLDER_DELETE_SELECTOR_FROM_STEP_1')
        ->assertDontSee('Zu löschende Seite');
});
```

Replace both `PLACEHOLDER_*_SELECTOR_FROM_STEP_1` strings with the actual selectors found in Step 1 before running — these are the one deliberate exception to this plan's no-placeholder rule, because the real selector depends on compiled markup this plan's research pass didn't fully resolve (`PageNavigationItem.vue`'s icon-button structure was flagged, not inspected line-by-line). Do not run the test with the placeholders still in place.

- [ ] **Step 3: Run, fix forward, restore, commit**

Run: `vendor/bin/pest tests/Browser/Admin/PagesCrudTest.php`
Expected: `Tests: 4 passed`.

```bash
git checkout -- public/admin
git add tests/Browser/Admin/PagesCrudTest.php
git commit -S -m "test: add Pages CRUD browser tests"
```

---

### Task 6: PersonsCrudTest.php

**Context:** Persons is structurally different: a drag-and-drop board (two columns, "Gemeinderat"/councilmen and "Angestellte"/employees) with no separate create route. Adding a person goes through a `vue-multiselect` taggable combobox; editing opens an inline sidebar form.

**Files:**
- Create: `tests/Browser/Admin/PersonsCrudTest.php`

**Interfaces:**
- Consumes: Task 1's dialog-handling pattern.
- Produces: nothing consumed by later tasks.

- [ ] **Step 1: Confirm the councilmen/employees grouping mechanism**

Persons are grouped into the two board columns via `Module` records (the same `Module` model Phase 1's Feature test harness introduced — check `database/factories/ModuleFactory.php` and `tests/Feature/Api/PersonsTest.php` for how a `Module` is seeded and how persons attach to one). Read `resources/easyadmin/src/pages/Persons.vue`'s data-loading logic (`mounted()`/`created()` and whatever action populates the two columns) to find the exact Module identifier/slug each column expects (the design research found a hardcoded string `"councilmen"` passed to `createPerson()` at `Persons.vue:276` — confirm what this string is compared against or looked up by).

- [ ] **Step 2: Rebuild, write the test**

Rebuild the admin SPA (Task 3 Step 1's command — the corrected env-var form). Create `tests/Browser/Admin/PersonsCrudTest.php`, seeding whatever `Module` record(s) Step 1 determined are required for the board to render its columns. **[Corrected during Task 1]** `visitAsAdmin()` replaces `asAdmin()` + `visit()` (the `Module`-seeding part of `beforeEach` stays — that's server-side setup, unrelated to the SPA's auth gate); delete test uses `->script('window.confirm = () => true;')`:

```php
<?php

use App\Models\Person;

beforeEach(function () {
    // Seed the Module record(s) Step 1 determined the Persons board requires
    // for its "Gemeinderat"/"Angestellte" columns to render — replace with the
    // real setup once confirmed (e.g. Module::factory()->create(['name' => '...'])).
});

it('lists existing persons', function () {
    Person::factory()->create(['name' => 'Maria Musterfrau']);

    visitAsAdmin('/admin/content/persons')
        ->assertSee('Maria Musterfrau');
});

it('creates a person via the multiselect tag input', function () {
    visitAsAdmin('/admin/content/persons')
        ->click('.add-member-btn')
        ->type('.multiselect__input', 'Neuer Bürger')
        ->keys('.multiselect__input', 'Enter')
        ->assertSee('Neuer Bürger');
});

it('edits an existing person via the sidebar form', function () {
    Person::factory()->create(['name' => 'Alter Name', 'phone' => '111']);

    visitAsAdmin('/admin/content/persons')
        // Click the edit icon within the person's card — second button in .actions,
        // per PersonCard.vue's DOM order [drag-handle, edit, delete].
        ->click('.actions button:nth-child(2)')
        ->fill('#personEntryPhone', '222')
        ->click('Speichern')
        ->assertDontSee('111');
});

it('deletes a person', function () {
    Person::factory()->create(['name' => 'Zu löschende Person']);

    $page = visitAsAdmin('/admin/content/persons')
        ->assertSee('Zu löschende Person');

    $page->script('window.confirm = () => true;');

    $page->click('.actions button:nth-child(3)')
        ->assertDontSee('Zu löschende Person');
});
```

Note: `.actions button:nth-child(2)`/`:nth-child(3)` are scoped to whichever `PersonCard` the click resolves against first — if multiple persons exist on the page, this selector is ambiguous. Since each test in this file creates exactly one person of interest, this works as written, but if `click()` matches the *first* `.actions button:nth-child(2)` on the page rather than one scoped to a specific card, and other persons exist (e.g. from a seeder), narrow the selector — e.g. by first asserting the target person's name is visible within a specific card container, then scoping the click to that same container (check what wrapping element `PersonCard.vue` renders, e.g. `.person-card`, and use a `:has-text()`-style or parent-scoped selector if the plugin supports one).

- [ ] **Step 3: Run, fix forward, restore, commit**

Run: `vendor/bin/pest tests/Browser/Admin/PersonsCrudTest.php`
Expected: `Tests: 4 passed`. This task's tests are the most likely in the whole plan to need iteration — `vue-multiselect`'s tag-creation interaction (Step 2's second test) was not empirically verified during design research (it rests on the library's documented public behavior, not inspected rendered DOM). If `keys('.multiselect__input', 'Enter')` doesn't create the tag, inspect the rendered DOM directly (`--debug` flag: `vendor/bin/pest tests/Browser/Admin/PersonsCrudTest.php --debug` opens a headed, pausable browser) to find the actual highlighted-option/confirmation element and click it directly instead of relying on the Enter keypress.

```bash
git checkout -- public/admin
git add tests/Browser/Admin/PersonsCrudTest.php
git commit -S -m "test: add Persons CRUD browser tests"
```

---

### Task 7: UsersCrudTest.php

**Context:** Users management is inline table-row editing (not a modal, not a separate route), reached via a header nav link ("Users", not the sidebar). New-row inputs have no `name`/`label`/`aria-label` — only table position is a reliable selector.

**Files:**
- Create: `tests/Browser/Admin/UsersCrudTest.php`

**Interfaces:**
- Consumes: Task 1's dialog-handling pattern.

- [ ] **Step 1: Rebuild, write the test**

Rebuild the admin SPA (Task 3 Step 1's command — the corrected env-var form). Create `tests/Browser/Admin/UsersCrudTest.php`. **[Corrected during Task 1]** `visitAsAdmin()`/`visitAsUser()` replace `beforeEach(fn () => asAdmin())`/`asUser()` + `visit()` (see Global Constraints — `asAdmin()`/`asUser()` alone don't satisfy the SPA's auth gate, so the non-admin test's original form would have always trivially passed by sitting on the login page rather than actually testing permission-based hiding); delete test uses `->script('window.confirm = () => true;')`:

```php
<?php

use App\Models\User;

it('lists existing users', function () {
    User::factory()->create(['name' => 'Bestehender Benutzer']);

    visitAsAdmin('/admin/users')
        ->assertSee('Bestehender Benutzer');
});

it('a non-admin cannot reach the users page', function () {
    visitAsUser('/admin/users')
        ->assertDontSee('Erstellen');
});

it('creates a user via the inline table row', function () {
    visitAsAdmin('/admin/users')
        ->click('Erstellen')
        ->fill('table tbody tr:first-child td:nth-child(1) input', 'Neuer Nutzer')
        ->fill('table tbody tr:first-child td:nth-child(2) input', 'neuer.nutzer@example.com')
        ->click('Speichern')
        ->assertSee('Neuer Nutzer');
});

it('edits an existing user', function () {
    $user = User::factory()->create(['name' => 'Alter Nutzername']);

    visitAsAdmin('/admin/users')
        // Scope to the row containing this user before clicking its edit action —
        // exact scoping mechanism depends on whether the plugin supports a
        // parent-relative selector; if not, and only one non-seed user exists,
        // a direct [aria-label="Bearbeiten"] click is unambiguous.
        ->click('[aria-label="Bearbeiten"]')
        ->fill('table tbody tr:first-child td:nth-child(1) input', 'Aktualisierter Name')
        ->click('Speichern')
        ->assertSee('Aktualisierter Name')
        ->assertDontSee('Alter Nutzername');
});

it('deletes a user', function () {
    User::factory()->create(['name' => 'Zu löschender Nutzer']);

    $page = visitAsAdmin('/admin/users')
        ->assertSee('Zu löschender Nutzer');

    $page->script('window.confirm = () => true;');

    $page->click('[aria-label="Löschen"]')
        ->assertDontSee('Zu löschender Nutzer');
});
```

Note: `visitAsAdmin()` creates a fresh admin per test, but the database seeder (`Tests\TestCase::setUp` calls `db:seed`, which creates 5 non-admin users per the Phase 1 harness's documented facts) means the users list is never empty — the "edits"/"deletes" tests' `[aria-label="Bearbeiten"]`/`[aria-label="Löschen"]` clicks may match a seeded user's row instead of the one just created if `click()` matches the first occurrence on the page. If a test fails because it modified the wrong row, this is the cause — scope the click more precisely (e.g. locate the row containing the target user's known name/email first, per whatever scoping mechanism Task 6 Step 3 resolves, since it's the same underlying problem).

- [ ] **Step 2: Run, fix forward, restore, commit**

Run: `vendor/bin/pest tests/Browser/Admin/UsersCrudTest.php`
Expected: `Tests: 5 passed`.

```bash
git checkout -- public/admin
git add tests/Browser/Admin/UsersCrudTest.php
git commit -S -m "test: add Users CRUD browser tests"
```

---

### Task 8: CI integration

**Files:**
- Modify: `.github/workflows/test.yml` (add a new `test-browser` job)

**Interfaces:**
- Consumes: nothing from earlier tasks except their combined `tests/Browser` suite.

- [ ] **Step 1: Add the new CI job**

In `.github/workflows/test.yml`, add a new job after `test-easyadmin`:

```yaml
  test-browser:
    name: Test Browser E2E
    runs-on: ubuntu-latest

    env:
      DB_CONNECTION: mysql
      DB_HOST: 127.0.0.1
      DB_PORT: 33306
      DB_DATABASE: test
      DB_USERNAME: test
      DB_PASSWORD: test

    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_DATABASE: test
          MYSQL_USER: test
          MYSQL_PASSWORD: test
          MYSQL_ROOT_PASSWORD: test
        ports:
          - 33306:3306
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - name: Checkout
        uses: actions/checkout@v7

      - name: Copy .env
        run: php -r "file_exists('.env') || copy('.env.testing', '.env');"

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite, gd
          coverage: none

      - name: Setup Node.js
        uses: actions/setup-node@v6
        with:
          node-version: "20.x"

      - name: Install PHP dependencies
        run: |
          composer install --no-interaction --no-suggest --prefer-dist --optimize-autoloader
          chmod -R 777 storage bootstrap/cache

      - name: Generate Application Key
        run: php artisan key:generate

      - name: Install root npm dependencies and Playwright
        run: |
          npm install
          npx playwright install --with-deps chromium

      - name: Build public site assets
        run: npm run production

      - name: Build admin SPA (test public path)
        working-directory: ./resources/easyadmin
        env:
          VUE_APP_PUBLIC_PATH: /admin/
          VUE_APP_API_HOST: ""
        run: |
          npm install
          npm run build

      - name: Run browser e2e tests
        run: vendor/bin/pest tests/Browser

      - name: Upload artifacts
        uses: actions/upload-artifact@master
        if: failure()
        with:
          name: Browser-Test-Logs
          path: ./storage/logs
```

- [ ] **Step 2: Validate the workflow YAML**

Run: `python3 -c "import yaml; yaml.safe_load(open('.github/workflows/test.yml'))"` (or any available YAML linter — this repo has no dedicated one; a successful parse is sufficient).
Expected: no error.

- [ ] **Step 3: Commit**

```bash
git add .github/workflows/test.yml
git commit -S -m "ci: add browser e2e test job"
```

---

### Task 9: Final verification

**Files:** none (verification only).

**Interfaces:**
- Consumes: everything from Tasks 1–8.

- [ ] **Step 1: Rebuild both frontends fresh**

```bash
npm run production
VUE_APP_PUBLIC_PATH=/admin/ VUE_APP_API_HOST= npm --prefix resources/easyadmin run build
```

- [ ] **Step 2: Run the full Browser suite**

Run: `vendor/bin/pest tests/Browser`
Expected: all tests pass (Task 1: 5, Task 2: 4, Task 3: 5, Task 4: 4, Task 5: 4, Task 6: 4, Task 7: 5 = 31 total, adjust if any task's iteration changed its final count).

- [ ] **Step 3: Confirm the existing suite is unaffected**

```bash
git checkout -- public/admin
vendor/bin/pest tests/Feature tests/Unit
```

Expected: `1 skipped, 133 passed (245 assertions)` — unchanged from before this branch.

- [ ] **Step 4: Review the full diff**

```bash
git log --oneline task/laravel-13-pest-4-upgrade..HEAD
git diff task/laravel-13-pest-4-upgrade..HEAD --stat
```

Expected: one commit per task above, touching only the files each task's **Files** section lists, plus `docs/superpowers/specs/2026-07-06-pest-browser-e2e-design.md` and this plan file from the design phase.
