<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('it returns a paginated list of events', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Event::factory()->count(30)->create();

    $response = getJson('/api/events');

    $response->assertSuccessful();
    $response->assertJsonCount(25, 'data');
});

test('it returns events within a date range', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Event::factory()->create(['date' => '2022-01-01']);
    Event::factory()->create(['date' => '2022-01-15']);
    Event::factory()->create(['date' => '2022-02-01']);

    $response = getJson('/api/events?startDate=2022-01-01&endDate=2022-01-31');

    $response->assertSuccessful();
    $response->assertJsonCount(2, 'data');
});

test('it stores a new event', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $data = [
        'date' => '2022-01-01',
        'text' => 'This is a test event',
    ];

    $response = $this->postJson('/api/events', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tbl_events', [
        'date' => '2022-01-01 00:00:00',
        'text' => 'This is a test event',
    ]);
});

test('it returns a validation error if the request is invalid', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $data = [
        'date' => 'not-a-date',
        'text' => '',
    ];

    $response = $this->postJson('/api/events', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['date', 'text']);
});

test('it shows a single event', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $event = Event::factory()->create();

    $response = $this->getJson("/api/events/{$event->ID}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['text' => $event->text]);
});

test('it updates an event', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $event = Event::factory()->create();

    $data = [
        'date' => '2022-02-02',
        'text' => 'This is an updated test event',
    ];

    $response = $this->putJson("/api/events/{$event->ID}", $data);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_events', [
        'date' => '2022-02-02 00:00:00',
        'text' => 'This is an updated test event',
    ]);
});

test('it deletes an event', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $event = Event::factory()->create();

    $response = $this->deleteJson("/api/events/{$event->ID}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tbl_events', ['ID' => $event->ID]);
});

test('it attaches a file to an event', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $event = Event::factory()->create();
    Storage::fake('local');
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson("/api/events/{$event->ID}/attach", [
        'file' => $file,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_downloads', [
        'attachable_id' => $event->ID,
        'attachable_type' => Event::class,
    ]);
});
