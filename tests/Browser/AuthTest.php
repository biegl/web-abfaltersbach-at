<?php

use App\Models\News;
use App\Models\Role;
use App\Models\User;

// Dialog handling: pest-plugin-browser v4.3.1 has no confirm()/alert() API anywhere in its
// source (checked Playwright/Page.php, Api/Webpage.php, Api/Concerns/*) and no README/CHANGELOG
// documenting one. Playwright's own default (no page.on('dialog', ...) listener registered) is
// to auto-DISMISS native dialogs, which would make confirm() return false and silently cancel
// every delete. The mechanism that worked: Webpage::script() runs arbitrary JS in the page after
// it has loaded (before the delete click), so `window.confirm = () => true` overrides the global
// before the click handler ever calls the native confirm() — the dialog event never fires at all.
// Every later delete test in this plan should call ->script('window.confirm = () => true;')
// once, right after visit()/navigate(), before clicking the delete control — as a STANDALONE
// statement, not chained: script() returns the JS eval result, not the Webpage, so chaining
// it breaks the following ->click(). See the delete test below for the working pattern.

it('redirects a guest to the login page', function () {
    // The admin SPA's assets must resolve under /admin/ in this single-document-root test
    // server, which forces the Vue Router base to /admin/ too — so every SPA route (including
    // this redirect target) is prefixed with /admin, unlike the brief's original assumption.
    visit('/admin')
        ->assertPathBeginsWith('/admin/login');
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
    // Not using asAdmin()/actingAs() here: this SPA's router guard gates on
    // sessionStorage['user'] (set only by a real login POST response, see
    // auth.module.ts/auth.service.ts), not on the Laravel session cookie pest-plugin-browser
    // injects from actingAs(). A pre-authenticated cookie alone still shows the login screen.
    // See the comment on this file's dialog-handling spike test for the corrected pattern.
    $user = User::factory()->create(['role' => Role::ADMIN]);

    visit('/admin/login')
        ->type('username', $user->email)
        ->type('password', 'password')
        ->click('Anmelden')
        ->assertSee('News')
        // Webpage has no visit() (that's a global test-helper that opens a fresh page/context,
        // dropping sessionStorage); navigate() is the same-page, address-bar-style method that
        // keeps sessionStorage intact across the hard reload our testing-only SPA route serves.
        ->navigate('/admin/logout')
        ->assertPathBeginsWith('/admin/login');
});

it('can delete an existing news item, proving native confirm() dialog handling', function () {
    // asAdmin()/actingAs() doesn't satisfy this SPA's client-side auth gate (see the comment on
    // the logout test above) — log in via the form instead.
    $user = User::factory()->create(['role' => Role::ADMIN]);
    $news = News::factory()->create(['title' => 'Löschen mich']);

    $page = visit('/admin/login')
        ->type('username', $user->email)
        ->type('password', 'password')
        ->click('Anmelden')
        ->navigate('/admin/content/news/overview')
        ->assertSee('Löschen mich');

    // script() returns the JS evaluation result, not the Webpage — call it standalone, not
    // chained, then keep using $page.
    $page->script('window.confirm = () => true;');

    $page->click('[aria-label="Löschen"]')
        ->assertDontSee('Löschen mich');
});
