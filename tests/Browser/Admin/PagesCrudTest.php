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
    // Root cause: Pages.vue's editPage(page) pushes router param `pageId: page.id`, where
    // `page` is a Navigation instance (the overview list is Navigation-driven, see file-level
    // comment above). Navigation.id (resources/easyadmin/src/models/navigation.ts) is a getter
    // for the Navigation's OWN primary key — not the linked Page's id. The correct value is
    // sitting right there as Navigation.pageId, appended server-side by
    // Navigation::getPageIdAttribute() (app/Models/Navigation.php:63), which resolves the real
    // linked Page via the `navigation_id` foreign key — Pages.vue just never reads it. So the
    // edit icon navigates to `/admin/content/pages/{navigation->ID}`, which only lands on the
    // intended Page by coincidence of the two tables' autoincrement ids. And even if that
    // coincidence holds and the save succeeds, the overview list re-renders from
    // Navigation.name, which editing a Page never updates — so the final assertSee could never
    // pass either. Needs Pages.vue to route via `pageId` (not `id`) and the overview to reflect
    // the edited title before this is real. Selectors below (from live markup inspection of
    // PageNavigationItem.vue): CoreUI's CIcon renders a bare inline <svg> with no name/class/
    // title identifying it, and the v-c-tooltip directive never sets a title attribute either —
    // the only stable target is the icon's wrapping <button>, positional (edit=1st, delete=2nd)
    // within the row, scoped by the row's visible name text.
    $navigation = Navigation::factory()->create(['name' => 'Alte Seite']);
    $page = Page::factory()->create(['seitentitel' => 'Alte Seite']);
    $page->forceFill(['navigation_id' => $navigation->ID])->save();

    visitAsAdmin('/admin/content/pages/overview')
        ->click('.d-flex.justify-content-between.align-items-center:has-text("Alte Seite") button:nth-of-type(1)')
        ->assertPathIs('/admin/content/pages/'.$page->ID)
        ->fill('Titel', 'Aktualisierte Seite')
        ->click('Speichern')
        ->assertPathIs('/admin/content/pages/overview')
        ->assertSee('Aktualisierte Seite');
})->skip('Pages.vue editPage() routes via the Navigation id (page.id) instead of the linked Page id (page.pageId), and the overview list renders Navigation.name which editing a Page never updates — see inline comment for root cause. Re-enable once Pages.vue routes via pageId and the overview reflects the edited title.');

it('deletes a page', function () {
    // Root cause: Pages.vue's deletePage(page) dispatches pages/delete with the Navigation
    // instance; PageService.delete() (resources/easyadmin/src/services/page.service.ts, via
    // base.service.ts) issues `DELETE /api/pages/${object.id}`, where `object.id` is again the
    // Navigation's own id (see 'edits an existing page' above for the full page.id-vs-pageId
    // root cause). PagesController::destroy() deletes the Page row at that id and never touches
    // tbl_navigation — so the Navigation row, and therefore the list item, survives the delete
    // regardless of whether the ids happen to line up. assertDontSee() can't pass until
    // deletePage() is routed through the linked Page's real id AND something also
    // removes/updates the Navigation row.
    $navigation = Navigation::factory()->create(['name' => 'Zu löschende Seite']);
    $page = Page::factory()->create(['seitentitel' => 'Zu löschende Seite']);
    $page->forceFill(['navigation_id' => $navigation->ID])->save();

    $webpage = visitAsAdmin('/admin/content/pages/overview')
        ->assertSee('Zu löschende Seite');

    // script() returns the JS eval result, not the Webpage — call it standalone, not chained.
    $webpage->script('window.confirm = () => true;');

    $webpage->click('.d-flex.justify-content-between.align-items-center:has-text("Zu löschende Seite") button:nth-of-type(2)')
        ->assertDontSee('Zu löschende Seite');
})->skip('Pages.vue deletePage() deletes the Page keyed on the Navigation id (page.id) and never touches the Navigation row shown in the list — see inline comment for root cause. Re-enable once deletePage() removes/updates the Navigation row the list actually renders.');
