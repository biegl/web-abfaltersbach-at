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
        // The #source-switch checkbox itself is unclickable (CoreUI's custom-control CSS
        // gives it z-index:-1), so click the visible label text instead.
        ->click('Source Editor')
        ->type('textarea.form-control', 'Neue Veranstaltung')
        ->click('Speichern')
        ->assertPathIs('/admin/content/events/overview')
        ->assertSee('Neue Veranstaltung');
});

it('edits an existing event', function () {
    $event = Event::factory()->create(['text' => 'Altes Fest']);

    visitAsAdmin('/admin/content/events/overview')
        ->click('.fa-edit')
        // Event::$primaryKey is 'ID' (see app/Models/Event.php), not Eloquent's default 'id' —
        // $event->id resolves to null and produces a bogus path assertion.
        ->assertPathIs('/admin/content/events/'.$event->ID)
        ->click('Source Editor')
        ->type('textarea.form-control', 'Aktualisiertes Fest')
        ->click('Speichern')
        ->assertPathIs('/admin/content/events/overview')
        ->assertSee('Aktualisiertes Fest')
        ->assertDontSee('Altes Fest');
});

it('deletes an event', function () {
    Event::factory()->create(['text' => 'Zu löschendes Fest']);

    $page = visitAsAdmin('/admin/content/events/overview')
        ->assertSee('Zu löschendes Fest');

    $page->script('window.confirm = () => true;');

    $page->click('[aria-label="Löschen"]')
        ->assertDontSee('Zu löschendes Fest');
});
