# Admin Panel Under /admin & Password-Reset Under /api Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make `/admin` the one real way to reach the admin panel in every environment (replacing the separate `admin.abfaltersbach.at` vhost and the `npm run serve` dev workflow), and move the password-reset routes under `/api`.

**Architecture:** A Laravel controller route serves the built admin SPA shell (`public/admin/index.html`) at `/admin/{any?}` unconditionally, replacing today's testing-only closure route. The admin SPA (`resources/easyadmin`) is rebuilt with a permanent `/admin/` `publicPath` and a same-origin (relative) API host, since it's now always served by the same Laravel app it calls. The password-reset routes move from `password/reset/*` to `api/password/reset/*` while staying on the `web` middleware group.

**Tech Stack:** Laravel 13 (PHP), Pest for tests, Vue 2/3 + Vue CLI (`resources/easyadmin`).

## Global Constraints

- Only `password/reset/{token}` (GET) and `password/reset` (POST) move under `/api`. `/files/{name}` and `/startseite` are explicitly out of scope and must not change.
- `npm run serve` is retired. `/admin` (served by Laravel) is the only way to reach the panel, in every environment — no separate dev-server origin.
- The `/admin` route must be a controller action, never a closure — `php artisan route:cache` (run on every production deploy per `deploy.yaml`) cannot serialize closure routes.
- `public/admin` stays committed to git. The deploy pipeline does not build frontend assets; it ships whatever is committed, so any `resources/easyadmin` change must be built and its output committed.
- Out of scope: the `admin.abfaltersbach.at` → `/admin` redirect (external, DNS/webserver), fixing the pre-existing dead reset-request link in `Login.vue`, and any change to `config/cors.php` or `config/sanctum.php`.

---

### Task 1: Serve `/admin` unconditionally via a cache-safe controller route

**Files:**
- Create: `app/Http/Controllers/AdminController.php`
- Modify: `routes/web.php`
- Modify: `app/Providers/RouteServiceProvider.php`
- Test: `tests/Feature/Web/AdminPanelTest.php`

**Interfaces:**
- Produces: `App\Http\Controllers\AdminController::index()` — returns `response()->file(public_path('admin/index.html'))`. `RouteServiceProvider::ADMIN` becomes the string `/admin` (previously `//admin.abfaltersbach.at`), consumed by `ResetPasswordController::$redirectTo` (Task 2's test asserts against this).

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Web/AdminPanelTest.php`:

```php
<?php

it('serves the admin SPA shell at /admin', function () {
    $this->get('/admin')
        ->assertStatus(200)
        ->assertHeader('content-disposition');
});

it('serves the admin SPA shell for any nested /admin path', function () {
    $this->get('/admin/content/news/overview')
        ->assertStatus(200)
        ->assertHeader('content-disposition');
});
```

- [ ] **Step 2: Run test to verify it currently passes for the wrong reason**

Run: `vendor/bin/pest tests/Feature/Web/AdminPanelTest.php`
Expected: PASS (2 tests) — `phpunit.xml` forces `APP_ENV=testing`, so the existing environment-gated closure route already serves this and the test can't distinguish closure-vs-controller by behavior alone. That's expected, not a problem: this test's job is to lock in the *observable* behavior (serving the SPA shell) while Steps 3–5 change the *mechanism* (closure → controller, no longer environment-gated). Step 7 is the real regression check for the actual bug being fixed.

- [ ] **Step 3: Create the controller**

Create `app/Http/Controllers/AdminController.php`:

```php
<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        return response()->file(public_path('admin/index.html'));
    }
}
```

- [ ] **Step 4: Register the route unconditionally, remove the closure**

In `routes/web.php`, add the import:

```php
use App\Http\Controllers\AdminController;
```

Replace:

```php
if (app()->environment('testing')) {
    Route::get('/admin/{any?}', function () {
        return response()->file(public_path('admin/index.html'));
    })->where('any', '.*');
}
```

with:

```php
// Serves the built admin SPA shell for every /admin path. Must be a controller action, not a
// closure: php artisan route:cache (run on every deploy) cannot serialize closure routes.
Route::get('/admin/{any?}', [AdminController::class, 'index'])->where('any', '.*');
```

Keep this route registered before `Route::fallback([PageController::class, 'show'])`, which stays last in the file.

- [ ] **Step 5: Update the ADMIN redirect constant**

In `app/Providers/RouteServiceProvider.php`, change:

```php
public const ADMIN = '//admin.abfaltersbach.at';
```

to:

```php
public const ADMIN = '/admin';
```

- [ ] **Step 6: Run test to verify it passes**

Run: `vendor/bin/pest tests/Feature/Web/AdminPanelTest.php`
Expected: PASS (2 tests)

- [ ] **Step 7: Verify the route survives route caching**

Run: `php artisan route:cache && php artisan route:clear`
Expected: `Routes cached successfully.` with no error. This is the regression this task exists to prevent — a closure route here would throw `Unable to prepare route [...] for serialization. Uses Closure.`

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/AdminController.php routes/web.php app/Providers/RouteServiceProvider.php tests/Feature/Web/AdminPanelTest.php
git commit -m "feat: serve /admin unconditionally via a cache-safe controller route"
```

---

### Task 2: Move password-reset routes under /api

**Files:**
- Modify: `routes/web.php`
- Test: `tests/Feature/Web/PasswordResetTest.php`

**Interfaces:**
- Consumes: `RouteServiceProvider::ADMIN` (Task 1) — asserted as the post-reset redirect target.
- Produces: routes named `password.reset` (GET `api/password/reset/{token}`) and `password.update` (POST `api/password/reset`) — unchanged names, so `App\Notifications\ResetPassword` (which builds its email link via `route('password.reset', [...])`) needs no code change.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Web/PasswordResetTest.php`:

```php
<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

it('shows the reset form at the new /api path', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);

    $this->get("api/password/reset/{$token}?email={$user->email}")
        ->assertStatus(200)
        ->assertViewIs('auth.passwords.reset');
});

it('resets the password and redirects to /admin', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);

    $this->post('api/password/reset', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ])->assertRedirect('/admin');

    expect(Hash::check('new-secret-password', $user->fresh()->password))->toBeTrue();
});

it('no longer serves the reset form at the old path', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);

    // PageController@show's 404 fallback returns view('errors.404') without an explicit
    // status code, so it's HTTP 200 — assert on the view, not the status (see
    // tests/Feature/Web/PageTest.php for the same pattern).
    $this->get("password/reset/{$token}?email={$user->email}")
        ->assertStatus(200)
        ->assertViewIs('errors.404');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/pest tests/Feature/Web/PasswordResetTest.php`
Expected: FAIL — `api/password/reset/*` doesn't exist yet, so the first two tests hit `routes/api.php`'s catch-all fallback (plain-text 404, not the reset form) instead of `auth.passwords.reset` / a redirect to `/admin`. The third test also fails at this point, since the *old* `password/reset/{token}` route still exists and still serves the form.

- [ ] **Step 3: Move the routes**

In `routes/web.php`, replace:

```php
// ResetPasswordController + resources/views/auth/passwords/reset.blade.php already exist
// (App\Models\User::sendPasswordResetNotification() depends on the "password.reset" route
// to build the emailed link) but were never wired up — restoring just these two, not the
// unrelated self-service "forgot password" request/email routes nothing currently triggers.
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
```

with:

```php
// ResetPasswordController + resources/views/auth/passwords/reset.blade.php already exist
// (App\Models\User::sendPasswordResetNotification() depends on the "password.reset" route
// to build the emailed link) but were never wired up — restoring just these two, not the
// unrelated self-service "forgot password" request/email routes nothing currently triggers.
// These stay on the 'web' middleware group (session + CSRF for the Blade form) even though
// the URI lives under /api/*, unlike everything registered through routes/api.php, which
// uses the stateless 'api' middleware group.
Route::get('api/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('api/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
```

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/pest tests/Feature/Web/PasswordResetTest.php`
Expected: PASS (3 tests)

- [ ] **Step 5: Commit**

```bash
git add routes/web.php tests/Feature/Web/PasswordResetTest.php
git commit -m "feat: move password-reset routes under /api"
```

---

### Task 3: Rebuild the admin SPA with a permanent /admin public path and same-origin API host

**Files:**
- Modify: `resources/easyadmin/vue.config.js`
- Modify: `resources/easyadmin/src/config.ts`
- Modify: `tests/Feature/Web/AdminPanelTest.php` (append one test)
- Rebuild: `public/admin/**` (generated, committed)

**Interfaces:**
- Consumes: `AdminController::index()` (Task 1) serves whatever is committed at `public/admin/index.html` — this task changes what gets committed there.

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/Web/AdminPanelTest.php`:

```php
it('is built with the /admin public path', function () {
    $html = file_get_contents(public_path('admin/index.html'));

    expect($html)->toContain('src="/admin/js/');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/pest tests/Feature/Web/AdminPanelTest.php`
Expected: FAIL — the currently-committed build uses root-absolute asset paths (`src="/js/..."`), not `/admin/js/...`.

- [ ] **Step 3: Set a permanent publicPath**

In `resources/easyadmin/vue.config.js`, replace:

```js
module.exports = {
    // @vue/cli-service 5.x has no `--public-path` CLI flag (publicPath is vue.config.js-only),
    // so browser tests override it via VUE_APP_PUBLIC_PATH (Vue CLI only inlines VUE_APP_* env
    // vars). Defaults to "/", matching the existing production build unchanged.
    publicPath: process.env.VUE_APP_PUBLIC_PATH || "/",
    runtimeCompiler: true,
```

with:

```js
module.exports = {
    // The admin SPA is always served by Laravel at /admin (see routes/web.php), never from
    // its own document root.
    publicPath: "/admin/",
    runtimeCompiler: true,
```

- [ ] **Step 4: Make the API host same-origin by default**

In `resources/easyadmin/src/config.ts`, replace:

```ts
    // VUE_APP_API_HOST lets browser tests point API calls at the embedded test server's own
    // origin (same-origin relative URL) instead of the hardcoded production/dev host below.
    // "??" (not "||") so an explicit empty-string override is respected.
    static host =
        process.env.VUE_APP_API_HOST ??
        (Config.isProduction ? "//abfaltersbach.at" : "//localhost");
```

with:

```ts
    // The admin panel is always served by the same Laravel app it calls (Sail locally, the
    // real domain in production) via /admin, so API calls are always same-origin.
    static host = "";
```

Leave `static isProduction = process.env.NODE_ENV === "production";` as-is — it's still used by `resources/easyadmin/src/components/FileInput.vue:49` for an unrelated debug flag.

- [ ] **Step 5: Rebuild and commit the admin SPA**

Run:

```bash
npm --prefix resources/easyadmin ci
npm --prefix resources/easyadmin run build
```

This runs the `postbuild` script (`rm -rf ../../public/admin && cp -R ./dist ../../public/admin`), replacing the committed build.

- [ ] **Step 6: Run test to verify it passes**

Run: `vendor/bin/pest tests/Feature/Web/AdminPanelTest.php`
Expected: PASS (4 tests)

- [ ] **Step 7: Commit**

```bash
git add resources/easyadmin/vue.config.js resources/easyadmin/src/config.ts public/admin tests/Feature/Web/AdminPanelTest.php
git commit -m "feat: rebuild admin SPA with a permanent /admin public path"
```

---

### Task 4: Retire now-redundant workflow, CI, and docs references

**Files:**
- Modify: `README.md`
- Modify: `.github/workflows/test.yml`
- Modify: `docs/superpowers/specs/2026-07-06-pest-browser-e2e-design.md`

No new test — these are documentation/CI changes with no runtime behavior to assert; Task 1–3's tests plus Task 5's full-suite run are the check that nothing broke.

- [ ] **Step 1: Update the README dev workflow**

In `README.md`, replace:

````
### Backend - EasyAdmin

The self-contained application is located in "resources/easyadmin"

```
npm run serve
```
````

with:

````
### Backend - EasyAdmin

The self-contained application is located in "resources/easyadmin". It's always served by
Laravel at `/admin` — there is no standalone dev server. After making changes, rebuild it and
reload `/admin` in the browser:

```
npm run build
```

Use `npx vue-cli-service build --watch` instead while iterating, to rebuild on save.
````

- [ ] **Step 2: Remove the now-redundant CI overrides**

In `.github/workflows/test.yml`, in the `test-browser` job, replace:

```yaml
      - name: Build admin SPA (test public path)
        working-directory: ./resources/easyadmin
        env:
          VUE_APP_PUBLIC_PATH: /admin/
          VUE_APP_API_HOST: ""
        run: |
          npm install
          npm run build
```

with:

```yaml
      - name: Build admin SPA
        working-directory: ./resources/easyadmin
        run: |
          npm install
          npm run build
```

- [ ] **Step 3: Mark the e2e design doc's divergence note superseded**

In `docs/superpowers/specs/2026-07-06-pest-browser-e2e-design.md`, find the bullet describing the test-only `--public-path` build (containing the sentence `"this is a deliberate divergence from the production build, not a claim about how production is actually configured, and does not touch the production build/deploy process."`) and add, directly after that bullet's paragraph, on its own line:

```
**Superseded by `docs/superpowers/specs/2026-07-08-admin-path-and-api-routes-design.md`:** `/admin/` is now the permanent, production `publicPath` (not a test-only divergence) — the admin SPA build is identical in tests, local dev, and production.
```

- [ ] **Step 4: Commit**

```bash
git add README.md .github/workflows/test.yml docs/superpowers/specs/2026-07-06-pest-browser-e2e-design.md
git commit -m "docs: retire npm run serve workflow and test-only admin build path notes"
```

---

### Task 5: Full verification

No files change in this task — it's the integration check across Tasks 1–4.

- [ ] **Step 1: Run the full backend test suite**

Run: `vendor/bin/pest`
Expected: all tests pass, including the new `tests/Feature/Web/AdminPanelTest.php` and `tests/Feature/Web/PasswordResetTest.php`.

- [ ] **Step 2: Run the browser e2e suite**

Run: `vendor/bin/pest tests/Browser`
Expected: passes using the now-default build config (no env var overrides needed — see Task 4, Step 2).

- [ ] **Step 3: Confirm route caching works end-to-end**

Run: `php artisan route:cache && php artisan route:list | grep -E "admin|password"`
Expected: no error from `route:cache`; `route:list` shows `GET|HEAD admin/{any?}`, `GET|HEAD api/password/reset/{token}`, `POST api/password/reset`. Run `php artisan route:clear` afterward to leave the app in its normal (uncached) local dev state.

- [ ] **Step 4: Manual smoke test (Sail)**

With `sail up -d` running:
1. Visit `/admin` — confirm the login screen renders (not the CMS 404 page).
2. Log in with a `role: 2` admin user (create one via `sail artisan tinker` if needed: `User::factory()->create(['role' => 2, 'email' => 'admin@test.com'])`, password `password`) and confirm the dashboard loads and an API-backed list (e.g. News) populates.
3. Visit any real CMS page (e.g. `/` or an existing navigation slug) — confirm it still renders normally, proving `Route::fallback` still works.
4. Trigger a password reset (`sail artisan tinker`: `$u = User::first(); $u->sendPasswordResetNotification(app('auth.password.broker')->createToken($u));` or check the Mailhog inbox at `localhost:8025` after using the app's real "forgot password" trigger if one exists) and confirm the emailed link points at `/api/password/reset/...` and the form works end-to-end, redirecting to `/admin` on success.
