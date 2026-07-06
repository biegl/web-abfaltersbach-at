<?php

use App\Models\Module;
use App\Models\Person;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => asUser());

it('lists persons', function () {
    Person::factory()->count(2)->create();

    $this->getJson('api/persons')->assertStatus(200);
});

it('creates a person', function () {
    $this->postJson('api/persons', [
        'name' => 'Anna', 'role' => 'Chef', 'phone' => '123', 'email' => 'a@b.c',
    ])
        ->assertStatus(201)
        ->assertJson(['name' => 'Anna']);
});

it('shows a person', function () {
    $person = Person::factory()->create();

    $this->getJson("api/persons/{$person->id}")
        ->assertStatus(200)
        ->assertJson(['id' => $person->id]);
});

it('returns 404 for a missing person', function () {
    $this->getJson('api/persons/999999')->assertStatus(404);
});

it('updates a person', function () {
    $person = Person::factory()->create();

    $this->putJson("api/persons/{$person->id}", ['name' => 'Renamed'])
        ->assertStatus(200)
        ->assertJson(['name' => 'Renamed']);
});

it('deletes a person', function () {
    $person = Person::factory()->create();

    $this->deleteJson("api/persons/{$person->id}")->assertStatus(204);
    $this->assertDatabaseMissing('persons', ['id' => $person->id]);
});

it('attaches an image to a person', function () {
    Storage::fake('attachments');
    $person = Person::factory()->create();

    $this->postJson("api/persons/{$person->id}/attach", [
        'file' => UploadedFile::fake()->image('portrait.jpg'),
    ])->assertStatus(200);
});

it('returns an empty list for a module without ids', function () {
    $module = Module::factory()->create(['configuration' => ['ids' => []]]);

    $this->getJson("api/persons/list/{$module->id}")
        ->assertStatus(200)
        ->assertExactJson([]);
});

it('validates the order on saveList', function () {
    $module = Module::factory()->create();

    // Non-existent person id fails the exists rule.
    $this->postJson("api/persons/list/{$module->id}", ['order' => [999999]])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['order.0']);
});

// FIELD() ordering is MySQL-only; skip its happy path on SQLite.
it('saves and returns an ordered person list', function () {
    $module = Module::factory()->create();
    $p1 = Person::factory()->create();
    $p2 = Person::factory()->create();

    $this->postJson("api/persons/list/{$module->id}", ['order' => [$p2->id, $p1->id]])
        ->assertStatus(200)
        ->assertJsonPath('0.id', $p2->id);
})->skip(fn () => DB::connection()->getDriverName() !== 'mysql', 'orderByRaw FIELD() is MySQL-only');
