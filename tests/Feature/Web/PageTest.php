<?php

use App\Models\Navigation;
use App\Models\Page;

it('redirects /startseite to the home page', function () {
    $this->get('/startseite')->assertRedirect('/');
});

it('renders a page resolved by its navigation slug', function () {
    // Router::findByUrl() resolves a URL to a page via the *Navigation* row's
    // own ID: Navigation::getUrlMap() maps url => $nav->ID, and Router then
    // calls Page::find($navId). So a nav item only renders if a Page with the
    // SAME primary key exists. That means both tables' autoincrement sequences
    // must advance in lockstep -- every nav row needs a paired page row so
    // nav.ID == page.ID. Create each pair together:
    //   startseite -> nav ID 1 / page ID 1
    //   ueber-uns  -> nav ID 2 / page ID 2  (the one under test)
    // Each page.navigation_id points back at its nav (used by
    // Navigation::breadcrumbs()/subnavigation()), and refID stays null
    // (top-level) -- a self-referential refID would infinite-loop through the
    // Navigation model's eager-loaded `children` relation.
    //
    // The startseite pair is also required because Navigation::breadcrumbs()
    // calls the `landingPage` scope; with no startseite it returns null, which
    // Laravel's `$scope(...) ?? $this` plumbing swaps for the query Builder,
    // later crashing array_unique(). A real site always has a startseite.
    $home = Navigation::factory()->create(['refID' => null, 'linkname' => 'startseite']);
    Page::factory()->create(['navigation_id' => $home->ID]);

    $nav = Navigation::factory()->create(['refID' => null, 'linkname' => 'ueber-uns']);
    $page = Page::factory()->create(['navigation_id' => $nav->ID]);

    // assertViewIs('page.default') proves the success branch ran: the 404
    // fallback returns view('errors.404') (also HTTP 200), so a status-only
    // assertion would pass even on fall-through. page.default is the view
    // PageController@show returns for a non-landing page (Page::templateName).
    $this->get('/'.$nav->linkname)
        ->assertStatus(200)
        ->assertViewIs('page.default')
        ->assertSee($page->seitentitel);
});

it('renders the 404 view for an unknown path', function () {
    // Fallback returns the 404 view but with a 200 status (see route map).
    $this->get('/no-such-page')->assertStatus(200)->assertViewIs('errors.404');
});
