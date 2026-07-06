<?php

use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => asUser());

it('lists events', function () {
    Event::factory()->count(3)->create();

    $this->getJson('api/events')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

it('filters events by date range', function () {
    Event::factory()->create(['date' => now()]);

    $this->getJson('api/events?startDate='.now()->subDay()->format('Y-m-d').'&endDate='.now()->addDay()->format('Y-m-d'))
        ->assertStatus(200);
});

it('creates an event', function () {
    $this->postJson('api/events', [
        'text' => 'Concert',
        'date' => now()->format('Y-m-d'),
    ])
        ->assertStatus(201)
        ->assertJson(['text' => 'Concert']);
});

it('validates an event on create', function () {
    $this->postJson('api/events', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['text', 'date']);
});

it('shows an event', function () {
    $event = Event::factory()->create();

    $this->getJson("api/events/{$event->ID}")
        ->assertStatus(200)
        ->assertJson(['ID' => $event->ID]);
});

it('returns 404 for a missing event', function () {
    $this->getJson('api/events/999999')->assertStatus(404);
});

it('updates an event', function () {
    $event = Event::factory()->create();

    $this->putJson("api/events/{$event->ID}", [
        'text' => 'Updated',
        'date' => now()->format('Y-m-d'),
    ])
        ->assertStatus(200)
        ->assertJson(['text' => 'Updated']);
});

it('deletes an event', function () {
    $event = Event::factory()->create();

    $this->deleteJson("api/events/{$event->ID}")->assertStatus(204);
    $this->assertDatabaseMissing('tbl_events', ['ID' => $event->ID]);
});

it('attaches a file to an event', function () {
    Storage::fake('attachments');
    $event = Event::factory()->create();

    $this->postJson("api/events/{$event->ID}/attach", [
        'file' => UploadedFile::fake()->create('flyer.pdf', 100),
    ])->assertStatus(200);

    expect($event->fresh()->attachments)->toHaveCount(1);
});
