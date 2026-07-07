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
        // The new row's role <select> has no default (User.vue: `new User()` leaves `role`
        // undefined) — a real admin has to pick one too.
        ->select('table tbody tr:first-child td:nth-child(4) select', '1')
        // The Save button is icon-only (no visible text, only aria-label/title="Speichern"),
        // so click('Speichern') can't find it via getByText — it must be targeted by attribute.
        ->click('[aria-label="Speichern"]')
        ->assertSee('Neuer Nutzer');
});

it('edits an existing user', function () {
    $user = User::factory()->create(['name' => 'Alter Nutzername']);

    visitAsAdmin('/admin/users')
        // The seeded 5 non-admin users (Phase 1 harness) plus the freshly logged-in admin
        // mean [aria-label="Bearbeiten"] alone would match the wrong row — scope to the row
        // containing this user's name before clicking its edit action.
        ->click('tr:has-text("Alter Nutzername") [aria-label="Bearbeiten"]')
        ->fill('tr:has(button[aria-label="Speichern"]) td:nth-child(1) input', 'Aktualisierter Name')
        ->click('[aria-label="Speichern"]')
        ->assertSee('Aktualisierter Name')
        ->assertDontSee('Alter Nutzername');
});

it('deletes a user', function () {
    User::factory()->create(['name' => 'Zu löschender Nutzer']);

    $page = visitAsAdmin('/admin/users')
        ->assertSee('Zu löschender Nutzer');

    $page->script('window.confirm = () => true;');

    $page->click('tr:has-text("Zu löschender Nutzer") [aria-label="Löschen"]')
        ->assertDontSee('Zu löschender Nutzer');
});
