<?php

use App\Models\Module;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // The Persons board doesn't look modules up by name/slug — it's hardcoded in
    // resources/easyadmin/src/store/persons.module.ts to Module row IDs 1 (councilmen)
    // and 2 (employees), fetched via GET /api/persons/list/{module} (route-model-bound
    // by primary key). Without both rows present, that request 404s. Creating them in
    // this order on a freshly migrated test DB yields IDs 1 and 2.
    $this->councilModule = Module::factory()->create(); // id 1: Gemeinderat/councilmen
    $this->employeeModule = Module::factory()->create(); // id 2: Angestellte/employees
});

it('lists existing persons', function () {
    // A Person row alone is invisible on this page — PersonCard only renders for
    // persons whose id is in a Module's configuration['ids'] (confirmed by loading the
    // page with an unattached person: both board columns stayed empty). Attaching the
    // person to the councilmen module mirrors what the real "add member" UI flow does
    // (it POSTs the same ids array to /api/persons/list/{module}).
    $person = Person::factory()->create(['name' => 'Maria Musterfrau']);
    $this->councilModule->update(['configuration' => ['ids' => [$person->id]]]);

    visitAsAdmin('/admin/content/persons')
        ->assertSee('Maria Musterfrau');
})->skip(fn () => DB::connection()->getDriverName() !== 'mysql', 'PersonsController::list() orderByRaw FIELD() is MySQL-only (see tests/Feature/Api/PersonsTest.php)');

it('creates a person via the multiselect tag input', function () {
    // Typing a tag and pressing Enter only opens the sidebar form with the name
    // pre-filled (Persons.vue::createPerson creates an unsaved draft Person and
    // selects it) — the name lives in an <input v-model>, which assertSee (Playwright
    // getByText) can't see. The person only becomes visible as page text, and gets
    // attached to a board column, once the form is actually submitted.
    // Both board columns render their own .add-member-btn — scope to the first
    // (Gemeinderat's) via Playwright's chained nth= selector syntax, since a bare
    // class selector resolves to 2 elements and click() requires a single match.
    visitAsAdmin('/admin/content/persons')
        ->click('.add-member-btn >> nth=0')
        ->type('.multiselect__input >> nth=0', 'Neuer Bürger')
        ->keys('.multiselect__input >> nth=0', 'Enter')
        ->click('Speichern')
        ->assertSee('Neuer Bürger');
});

it('edits an existing person via the sidebar form', function () {
    $person = Person::factory()->create(['name' => 'Alter Name']);
    $this->councilModule->update(['configuration' => ['ids' => [$person->id]]]);

    // PersonCard.vue only renders {{ person.name }} and {{ person.role }} as visible
    // text (no phone/email), so the edit must be asserted on the name — editing phone
    // wouldn't move the needle on the page's visible text either way.
    visitAsAdmin('/admin/content/persons')
        // Click the edit icon within the person's card — second button in .actions,
        // per PersonCard.vue's DOM order [drag-handle, edit, delete].
        ->click('.actions button:nth-child(2)')
        ->fill('#personEntryName', 'Neuer Name')
        ->click('Speichern')
        ->assertSee('Neuer Name')
        ->assertDontSee('Alter Name');
})->skip(fn () => DB::connection()->getDriverName() !== 'mysql', 'PersonsController::list() orderByRaw FIELD() is MySQL-only (see tests/Feature/Api/PersonsTest.php) — needed just to render the card to click');

it('deletes a person', function () {
    $person = Person::factory()->create(['name' => 'Zu löschende Person']);
    $this->councilModule->update(['configuration' => ['ids' => [$person->id]]]);

    $page = visitAsAdmin('/admin/content/persons')
        ->assertSee('Zu löschende Person');

    $page->script('window.confirm = () => true;');

    $page->click('.actions button:nth-child(3)')
        ->assertDontSee('Zu löschende Person');
})->skip(fn () => DB::connection()->getDriverName() !== 'mysql', 'PersonsController::list() orderByRaw FIELD() is MySQL-only (see tests/Feature/Api/PersonsTest.php) — needed just to render the card to click');
