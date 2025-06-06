<?php

use App\Models\Module;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

test('it returns a list of persons', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Person::factory()->count(5)->create();

    $response = getJson('/api/persons');

    $response->assertSuccessful();
    $response->assertJsonCount(5, 'data');
});

test('it stores a new person', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $data = [
        'name' => 'John Doe',
        'role' => 'Developer',
        'phone' => '123456789',
        'email' => 'john.doe@example.com',
    ];

    $response = postJson('/api/persons', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('persons', $data);
});

test('it shows a single person', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $person = Person::factory()->create();

    $response = getJson("/api/persons/{$person->id}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['name' => $person->name]);
});

test('it updates a person', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $person = Person::factory()->create();

    $data = [
        'name' => 'Jane Doe',
        'role' => 'Designer',
    ];

    $response = putJson("/api/persons/{$person->id}", $data);

    $response->assertSuccessful();
    $this->assertDatabaseHas('persons', $data);
});

test('it deletes a person', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $person = Person::factory()->create();

    $response = deleteJson("/api/persons/{$person->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('persons', ['id' => $person->id]);
});

test('it attaches a file to a person', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $person = Person::factory()->create();
    Storage::fake('local');
    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson("/api/persons/{$person->id}/attach", [
        'file' => $file,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_downloads', [
        'attachable_id' => $person->id,
        'attachable_type' => Person::class,
    ]);
});

test('it returns a list of persons for a module', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $persons = Person::factory()->count(3)->create();
    $module = Module::factory()->create([
        'configuration' => ['ids' => $persons->pluck('id')->toArray()],
    ]);

    $response = getJson("/api/persons/list/{$module->id}");

    $response->assertSuccessful();
    $response->assertJsonCount(3);
    $response->assertJsonPath('0.id', $persons[0]->id);
    $response->assertJsonPath('1.id', $persons[1]->id);
    $response->assertJsonPath('2.id', $persons[2]->id);
});

test('it saves a list of persons for a module', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $persons = Person::factory()->count(3)->create();
    $module = Module::factory()->create();
    $orderedIds = $persons->pluck('id')->toArray();

    $response = postJson("/api/persons/list/{$module->id}", [
        'order' => $orderedIds,
    ]);

    $response->assertSuccessful();
    $module->refresh();
    expect($module->configuration['ids'])->toEqual($orderedIds);
});
