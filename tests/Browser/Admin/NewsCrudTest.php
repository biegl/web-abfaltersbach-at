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
        // The #source-switch checkbox itself is unclickable: CoreUI's custom-control CSS gives
        // it z-index:-1 (stacked behind its label so it never overlays the label text), which
        // fails Playwright's actionability check. Click the visible label text instead.
        ->click('Source Editor')
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
        // News::$primaryKey is 'ID' (see app/Models/News.php), not Eloquent's default 'id' —
        // $news->id resolves to null and produces a bogus path assertion.
        ->assertPathIs('/admin/content/news/'.$news->ID)
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

    // script() returns the JS evaluation result, not the Webpage — call it standalone, not
    // chained, then keep using $page. See tests/Browser/AuthTest.php for the full "why".
    $page->script('window.confirm = () => true;');

    $page->click('[aria-label="Löschen"]')
        ->assertDontSee('Zu löschende Meldung');
});

it('attaches a file to an existing news item via the Uppy widget', function () {
    $news = News::factory()->create();
    $filePath = base_path('tests/Browser/fixtures/sample.jpg');

    visitAsAdmin('/admin/content/news/'.$news->ID)
        ->attach('.uppy-FileInput-input', $filePath)
        ->assertSee('Upload erfolgreich');
})->skip(
    // pest-plugin-browser v4.3.1's attach()/Locator::setInputFiles() always sends the file as
    // a 'localPaths' param over the wire. Playwright's WS "run-server" mode (the only mode
    // ServerManager.php uses) never marks the connecting client as collocated with the server
    // (createPlaywright() is built with isServer: true, not isClientCollocatedWithServer: true),
    // so Playwright rejects it server-side with "localPaths are not allowed when the client is
    // not local" — confirmed this is not a version-skew bug by reproducing the identical failure
    // after downgrading playwright-core to 4.3.1's own declared minimum (1.59.1). This is a
    // structural limitation of the plugin's file-upload support, not a selector/text problem:
    // any local-path file upload test in this harness will hit the same wall. Re-enable once
    // pest-plugin-browser ships a payloads-based (in-memory) setInputFiles, or once Playwright
    // gains a supported way to mark the WS client as collocated.
    'attach() cannot upload local files over pest-plugin-browser\'s WS server transport'
);
