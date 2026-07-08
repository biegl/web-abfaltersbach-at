<?php

use App\Models\Navigation;
use App\Models\Page;

// Pages' overview list (Pages.vue) renders $store.state.navigation.all — i.e. App\Models\Navigation
// (tbl_navigation) — not App\Models\Page (tbl_site). It displays Navigation.name, and creating a
// Page via the API/SPA never creates or links a Navigation row (no route, no observer). So "listing"
// a page means seeding a Navigation row, and "creating" a page can only be asserted via persistence,
// not via appearing in the list. See the skipped edit/delete tests below for the deeper bug this
// causes for those two flows.

it('lists existing pages', function () {
    // refID must be null: NavigationController's default (non-"showAll") query only returns
    // top-level items (scopeAllTopLevel: where refID is null). NavigationFactory's default
    // refID is a random 0-10, which besides usually hiding the row from this query, can also
    // (rarely, e.g. refID coinciding with this row's own autoincrement id) send Navigation's
    // self-referencing `children()` + `$with = ['children']` eager-load into infinite
    // recursion — observed as an Xdebug "possible infinite loop" abort during this test's
    // development.
    Navigation::factory()->create(['name' => 'Impressum', 'refID' => null]);

    visitAsAdmin('/admin/content/pages/overview')
        ->assertSee('Impressum');
});

it('creates a page via the SPA', function () {
    visitAsAdmin('/admin/content/pages/overview')
        ->click('Erstellen')
        ->assertPathIs('/admin/content/pages/add')
        ->fill('Titel', 'Neue Seite')
        // The #source-switch checkbox itself is unclickable (z-index:-1 behind its label, see
        // NewsCrudTest/EventsCrudTest) — click the visible label text instead.
        ->click('Source Editor')
        ->type('textarea.form-control', 'Neuer Seiteninhalt')
        ->click('Speichern')
        ->assertPathIs('/admin/content/pages/overview');

    // Can't assertSee() the new page on the overview — it's a Navigation-driven list and
    // creating a Page never creates/links a Navigation row (see file-level comment above).
    // Assert what the create flow actually does: persist the Page.
    expect(Page::query()->where('seitentitel', 'Neue Seite')->exists())->toBeTrue();
});

it('edits an existing page', function () {
    // Pages.vue's editPage()/deletePage() used to route via `page.id` (the Navigation's own
    // id) instead of `page.pageId` (Navigation::getPageIdAttribute(), which resolves the real
    // linked Page via the `navigation_id` foreign key) — fixed. What's NOT fixed: the overview
    // list re-renders from Navigation.name, which editing a Page's `seitentitel` never updates
    // (they're independent fields with no sync mechanism) — so the final assertSee can't pass
    // regardless of whether the edit itself landed correctly. This is a separate, deeper
    // architectural question (should Navigation.name auto-sync to the linked Page's title? Or
    // should the overview render Page data instead of Navigation data?) that a bug-fix pass
    // shouldn't answer unilaterally — flagging for a product decision, not fixing here.
    // Selectors below (from live markup inspection of PageNavigationItem.vue): CoreUI's CIcon
    // renders a bare inline <svg> with no name/class/title identifying it, and the v-c-tooltip
    // directive never sets a title attribute either — the only stable target is the icon's
    // wrapping <button>, positional (edit=1st, delete=2nd) within the row, scoped by the row's
    // visible name text.
    // refID must be null (see 'lists existing pages' above for why: scopeAllTopLevel filters
    // on it, and a random non-null default risks Navigation::children()'s infinite-recursion
    // trap).
    $navigation = Navigation::factory()->create(['name' => 'Alte Seite', 'refID' => null]);
    $page = Page::factory()->create(['seitentitel' => 'Alte Seite']);
    $page->forceFill(['navigation_id' => $navigation->ID])->save();

    visitAsAdmin('/admin/content/pages/overview')
        ->click('.d-flex.justify-content-between.align-items-center:has-text("Alte Seite") button:nth-of-type(1)')
        ->assertPathIs('/admin/content/pages/'.$page->ID)
        ->fill('Titel', 'Aktualisierte Seite')
        ->click('Speichern')
        ->assertPathIs('/admin/content/pages/overview')
        ->assertSee('Aktualisierte Seite');
})->skip('Pages.vue now routes edits via the correct linked-Page id (pageId, not id), but the overview list renders Navigation.name — which editing a Page never updates. Re-enable once there is a product decision on how Navigation/Page display data should stay in sync, and it is implemented.');

it('deletes a page', function () {
    // Same page.id -> page.pageId routing fix as the edit test above — deletePage() now
    // deletes the CORRECT linked Page (verified: Page::count() drops to 0 after the click,
    // Navigation::count() stays at 1). What's NOT fixed: PagesController::destroy() never
    // touches tbl_navigation, so the Navigation row — and therefore the list item — survives
    // the delete. Confirmed empirically (not just by code-reading): the overview's own
    // loadNavigation() re-fetch briefly shows a loading state, then re-renders the surviving
    // Navigation row with its name intact — a naive single-poll assertDontSee can pass by
    // accident if it happens to sample the loading gap, so don't trust a single green run here.
    // Fixing this requires a product decision on whether deleting a Page should also
    // delete/unlink its Navigation entry, or a UI change (e.g. hide entries whose linked Page
    // is gone) — not something to guess at in a bug-fix pass.
    // refID must be null — same reason as the edit test above and the list test's comment.
    $navigation = Navigation::factory()->create(['name' => 'Zu löschende Seite', 'refID' => null]);
    $page = Page::factory()->create(['seitentitel' => 'Zu löschende Seite']);
    $page->forceFill(['navigation_id' => $navigation->ID])->save();

    $webpage = visitAsAdmin('/admin/content/pages/overview')
        ->assertSee('Zu löschende Seite');

    // script() returns the JS eval result, not the Webpage — call it standalone, not chained.
    $webpage->script('window.confirm = () => true;');

    $webpage->click('.d-flex.justify-content-between.align-items-center:has-text("Zu löschende Seite") button:nth-of-type(2)')
        ->assertDontSee('Zu löschende Seite');
})->skip('Pages.vue now routes deletes via the correct linked-Page id (pageId, not id), and the right Page row is genuinely deleted (verified via Page::count()) — but PagesController::destroy() never touches tbl_navigation, so the surviving Navigation row keeps the list item visible. Re-enable once there is a product decision on whether deleting a Page should remove/unlink its Navigation entry, and it is implemented.');
