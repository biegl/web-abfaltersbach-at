<?php

use App\Models\Event;
use App\Models\File;
use App\Models\Navigation;
use App\Models\Page;
use Carbon\Carbon;

it('renders the home page with the event list', function () {
    // Router::findByUrl() maps a URL to a page via the *Navigation* row's own
    // ID (Navigation::getUrlMap() keys by $nav->ID, then Router calls
    // Page::find($navId)) -- so the Page only renders if a Page with the SAME
    // primary key exists. Both tables are freshly migrated per test, so
    // creating exactly one of each in order gives matching IDs. refID must
    // stay null (top-level): the factory default is a random small int, and a
    // self-referential refID would infinite-loop through Navigation's
    // eager-loaded `children` relation.
    $nav = Navigation::factory()->create(['refID' => null, 'linkname' => 'startseite']);
    $page = Page::factory()->create(['navigation_id' => $nav->ID, 'template' => 'template_home.php']);

    // Router::$urlMap is a static, process-lifetime cache with no invalidation
    // hook on Navigation writes -- across visit() calls in the same browser
    // test server process, a URL map built before this row existed would
    // never pick it up. Force a rebuild so the new page resolves.
    (new App\Router\Router)->clearCache();

    // Only a page.home template (Page::isLandingPage) renders the event list;
    // scopeUpcoming() only picks up events between tomorrow and +3 months, so
    // "today" never appears in the grouped-by-month list -- use tomorrow.
    $eventDate = Carbon::tomorrow();
    Event::factory()->create([
        'date' => $eventDate,
        'text' => 'Ein Testereignis',
    ]);

    // Not asserting assertNoBrokenImages(): the footer (partials/footer.blade.php,
    // rendered on every page) embeds a live webcam image and a Google Static Maps
    // image from real external hosts, unrelated to this page's fixtures -- those
    // are unreachable/broken in this sandboxed test environment regardless of what
    // data the test sets up.
    visit('/')
        ->assertSee($eventDate->translatedFormat('F'))
        ->assertSee('Ein Testereignis');
});

it('navigates to a page via its navigation slug', function () {
    // Navigation::breadcrumbs() always looks up the landingPage scope
    // (linkname = 'startseite'); with none in the DB, the scope's ->first()
    // returns null, and Eloquent's `$scope(...) ?? $this` plumbing swaps that
    // null for the query Builder, which later crashes array_unique(). A real
    // site always has a startseite, so pair one up here too (see the same
    // note in tests/Feature/Web/PageTest.php).
    $home = Navigation::factory()->create(['refID' => null, 'linkname' => 'startseite']);
    Page::factory()->create(['navigation_id' => $home->ID, 'template' => 'template_home.php']);

    $nav = Navigation::factory()->create(['refID' => null, 'linkname' => 'ueber-uns']);
    $page = Page::factory()->create(['navigation_id' => $nav->ID, 'inhalt' => 'Einzigartiger Seiteninhalt']);

    (new App\Router\Router)->clearCache();

    visit('/ueber-uns')
        ->assertSee('Einzigartiger Seiteninhalt');
});

it('renders the 404 view for an unknown path', function () {
    // errors.404 never prints the literal string "404" -- it renders the
    // page.404.headline translation string.
    visit('/this-path-does-not-exist')
        ->assertSee(__('page.404.headline'));
});

it('downloads a file via its public link', function () {
    // FilesController::download() looks a File up by title OR file column;
    // use a clean title (the factory default is a multi-word sentence) so
    // the URL segment is unambiguous.
    $file = File::factory()->create(['title' => 'brochure']);

    visit('/files/'.$file->title)
        ->assertNoJavaScriptErrors();
});
