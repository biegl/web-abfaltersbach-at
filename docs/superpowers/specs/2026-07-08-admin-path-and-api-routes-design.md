# Admin panel under `/admin`, backend routes under `/api` — Design

## Context

The admin SPA (`resources/easyadmin`) is currently served two different ways depending on environment, neither of which is `/admin` on the main domain:

- **Production**: a separate `admin.abfaltersbach.at` vhost whose document root is `public/admin` (a pre-built, git-committed Vue app, built with the default `publicPath: /`). Laravel's router is not involved at all.
- **Local dev**: only reachable via the Vue CLI dev server (`npm run serve`, port 8080). A `/admin/{any?}` route exists in `routes/web.php` but is gated to `app()->environment('testing')` — it exists purely as a fixture for the Pest browser e2e suite and was never meant for interactive use.

The user will separately set up a redirect from `https://admin.abfaltersbach.at` to `https://abfaltersbach.at/admin` at the DNS/webserver level (out of scope for this repo). This work makes `/admin` on the main app the one real way to reach the panel, in every environment, so that redirect has something correct to land on.

Separately, the JSON API is already fully namespaced under `/api` (`routes/api.php`, auto-prefixed by `RouteServiceProvider`). The only routes outside `/api` are `/startseite` (a vanity redirect), `/files/{name}` (a public file-download link embedded in already-published CMS content), and `password/reset/{token}` GET + `password/reset` POST (a classic session/CSRF Blade form). This work moves the password-reset routes under `/api`; `/files/{name}` and `/startseite` are explicitly left alone.

## Decisions

1. **`npm run serve` is retired entirely.** There is one way to run the admin panel, in every environment: build it (`npm run build` in `resources/easyadmin`, or a `--watch` variant while iterating), then load `/admin` through Laravel. No separate dev-server origin.
2. **Only `password/reset/{token}` and `password/reset` move under `/api`.** `/files/{name}` stays exactly where it is — moving it would change URLs already embedded in published CMS content (news/pages, TinyMCE-uploaded images) and collide with the existing authenticated `/api/files/{id}` JSON resource route.

## 1. Serving `/admin` everywhere

**Mechanism: a Laravel route, not a webserver rewrite.** An Apache `.htaccess`-level rewrite (matching how the old subdomain vhost behaves today) would not work locally, since Sail runs via `php artisan serve`, which never reads `.htaccess`. A Laravel route works identically in Sail and in production, and is the exact mechanism already proven by the Pest browser e2e suite.

**Closure → controller (required, not optional).** The existing `/admin/{any?}` route is a Closure, only ever registered under `APP_ENV=testing`. That guard is *why* it has gotten away with being a closure: `php artisan route:cache` (run during every production deploy, per `deploy.yaml`) never sees it, because deploys don't run under `testing`. Laravel's route cache cannot serialize closures — if the environment guard is simply removed, the very next `route:cache` during deploy throws (`Unable to prepare route for serialization. Uses Closure.`) and breaks routing for the *entire app*, not just `/admin`.

**Changes:**
- Add a small controller action (e.g. a method on a new `AdminController`, or an existing suitable controller) that returns `response()->file(public_path('admin/index.html'))`.
- Register it unconditionally in `routes/web.php` as `Route::get('/admin/{any?}', [Controller::class, 'method'])->where('any', '.*')`, positioned before `Route::fallback([PageController::class, 'show'])` so it isn't swallowed by CMS page-slug resolution. Remove the `app()->environment('testing')` guard.
- Update `RouteServiceProvider::ADMIN` from `'//admin.abfaltersbach.at'` to `'/admin'`. It's used only as the post-password-reset-completion redirect target (`ResetPasswordController::$redirectTo`).

## 2. Dev workflow & build changes

Retiring `npm run serve` (decision 1) has a real correctness consequence, not just a workflow change:

- **`resources/easyadmin/vue.config.js`**: set `publicPath: '/admin/'` permanently (currently defaults to `/`, which is only correct when the app is served from its own vhost root — true today, not true once it's nested under the main app at `/admin`).
- **`resources/easyadmin/src/config.ts` (`Config.host`)**: today this branches on `NODE_ENV === 'production'` to pick `//abfaltersbach.at` (prod) vs `//localhost` (dev), because dev used to mean `npm run serve` (`NODE_ENV=development`) hitting a Sail backend at `localhost`. Every `npm run build` sets `NODE_ENV=production` — once that is the *only* build path, this branch always resolves to the hardcoded production host, `//abfaltersbach.at`, even for a local Sail build reached at `abfaltersbach.test`. That would silently point a local admin panel's API calls at the real production backend. Fix: since `/admin` and `/api` are now always served by the same Laravel app on the same origin (Sail locally, the real domain in production), drop the environment branching and use a relative/same-origin host. This also removes the need for the `VUE_APP_API_HOST` override, which exists today solely to work around this for the browser e2e build.
- **README**: replace the "Backend - EasyAdmin: `npm run serve`" section with the build-and-reload workflow (`npm run build`, optionally with `--watch`, then reload `/admin`).
- **`public/admin` stays committed to git**, as today — the deploy pipeline does not build frontend assets; it ships whatever is committed. A developer (or CI, if that changes later — not part of this work) must run the build and commit the result after any `resources/easyadmin` change.
- **CI (`.github/workflows/test.yml`)**: the `test-browser` job's `VUE_APP_PUBLIC_PATH: /admin/` and `VUE_APP_API_HOST: ""` env overrides become identical to the new defaults — remove them. `test-easyadmin` and `test-app` jobs are unaffected.
- **`docs/superpowers/specs/2026-07-06-pest-browser-e2e-design.md`**: its "Confirmed Facts" section documents building the admin SPA with a *test-only* corrected public path, explicitly called out as "a deliberate divergence from the production build... does not touch the production build/deploy process." That statement becomes false — the test build and the real build are now the same. Add a note marking that section superseded by this design.

## 3. Backend routes under `/api`

`password/reset/{token}` (GET) and `password/reset` (POST) move from `password/reset/...` to `api/password/reset/...`. They remain declared in `routes/web.php` under the `web` middleware group — they render a Blade form and need session + CSRF, which `routes/api.php`'s group (stateless, `api` middleware) does not provide. This means a `web`-middleware route intentionally lives under the `/api/*` URL prefix without going through `routes/api.php`; add a short comment at the declaration explaining why, since it's non-obvious from the code alone.

No other code changes are needed: the reset email builds its link via the named route `password.reset` (`Notifications\ResetPassword` → `route('password.reset', [...])`), so it picks up the new URI automatically. No existing test references the literal path, and there is no existing test coverage of the password-reset flow at all — add one small feature test asserting the new path works (GET renders the form, POST resets the password), since this is an auth-adjacent path worth one regression check.

`/files/{name}` and `/startseite` are explicitly left where they are — not moved, not renamed.

**Found in passing, not touched:** `resources/easyadmin/src/pages/Login.vue` has a link `<a href="/password/reset">` (no token) pointing at a self-service "request a reset link" page that was deliberately never built (per the existing comment in `web.php`). It is already a dead link today and stays that way; fixing it is out of scope for this work.

## 4. Deployment sequencing (external, flagged not owned)

Today, `admin.abfaltersbach.at`'s vhost document root *is* `public/admin`, built with root-absolute asset paths (`/js/...`, `/css/...`) — correct only because that vhost's root is that directory. Once `publicPath: /admin/` ships, those paths become `/admin/js/...`, `/admin/css/...`. From the moment this deploys, the **old subdomain will 404 its own assets** (there is no `/admin` subfolder inside `public/admin`) until the redirect to `abfaltersbach.at/admin` is in place. The redirect is the user's responsibility (per Context above) — noting here only so the deploy and the redirect are done in the same window, not so this repo takes on that work.

## 5. Verification

- `php artisan route:cache` succeeds after the closure → controller conversion (proves the route-cache break is actually fixed, not just untested).
- `php artisan route:list` shows `/admin/{any?}` registered unconditionally, and `api/password/reset/{token}` + `api/password/reset`.
- Manual, local (Sail): `/admin` loads the built SPA and logs in against the local DB; a real CMS page still renders through `Route::fallback`; `/api/password/reset/{token}` renders the reset form and a submission succeeds.
- New feature test for the password-reset routes at their new path passes.
- Existing Pest browser e2e suite (`tests/Browser`) passes with the build-step simplification (no more special-cased public path / API host env vars).

## Out of scope

- The `admin.abfaltersbach.at` → `/admin` redirect itself (DNS/webserver config, external to this repo).
- Moving `/files/{name}` or `/startseite` under `/api`.
- Fixing the dead "request a reset link" link in `Login.vue`, or building the self-service reset-request flow it points at.
- Any change to `config/cors.php` or `config/sanctum.php` (the password-reset route moving under `/api/*` picks up whatever CORS handling already applies there; not expected to matter for a same-origin browser page load, and not something this work needs to tune).
