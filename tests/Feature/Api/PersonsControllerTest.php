<?php

use App\Models\Module;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

test('it returns a list of persons', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Person::factory()->count(5)->create();

    $response = getJson('/api/persons');

    $response->assertSuccessful();
    $response->assertJsonCount(5, 'data');
});

test('it returns a paginated list of persons', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Person::factory()->count(15)->create();

    $response = getJson('/api/persons?itemsPerPage=5');

    $response->assertSuccessful();
    $response->assertJsonCount(5, 'data');
    $response->assertJsonStructure(['total', 'per_page', 'current_page', 'data']);
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

test('it deletes a person with an image', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $person = Person::factory()->create();
    Storage::fake('attachments');
    $file = UploadedFile::fake()->image('avatar.jpg');

    // Attach the file
    $response = $this->postJson("/api/persons/{$person->id}/attach", ['file' => $file]);
    $fileRecord = $person->image()->first();
    Storage::disk('attachments')->assertExists(str_replace('/upload', '', $fileRecord->file));

    // Delete the person
    $response = deleteJson("/api/persons/{$person->id}");
    $response->assertStatus(204);

    // Assertions
    $this->assertDatabaseMissing('persons', ['id' => $person->id]);
    $this->assertDatabaseMissing('tbl_downloads', ['id' => $fileRecord->id]);
    Storage::disk('attachments')->assertMissing(str_replace('/upload', '', $fileRecord->file));
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

test('it returns an empty list for a module with no persons', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $module = Module::factory()->create(['configuration' => ['ids' => []]]);

    $response = getJson("/api/persons/list/{$module->id}");

    $response->assertSuccessful();
    $response->assertJsonCount(0);
});

test('it returns a sorted list of persons for a module', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $persons = Person::factory()->count(3)->create();
    $sortedIds = $persons->pluck('id')->shuffle()->toArray();
    $module = Module::factory()->create([
        'configuration' => ['ids' => $sortedIds],
    ]);

    $response = getJson("/api/persons/list/{$module->id}");

    $response->assertSuccessful();
    $response->assertJsonCount(3);
    $response->assertJsonPath('0.id', $sortedIds[0]);
    $response->assertJsonPath('1.id', $sortedIds[1]);
    $response->assertJsonPath('2.id', $sortedIds[2]);
});

test('it returns 404 if module not found in list', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $response = getJson('/api/persons/list/999');

    $response->assertStatus(404);
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
