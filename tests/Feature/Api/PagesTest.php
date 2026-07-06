<?php

use App\Models\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => asUser());

it('lists pages', function () {
    Page::factory()->count(2)->create();

    $this->getJson('api/pages')->assertStatus(200);
});

it('creates a page', function () {
    $this->postJson('api/pages', ['seitentitel' => 'About', 'inhalt' => 'Body'])
        ->assertStatus(201)
        ->assertJson(['seitentitel' => 'About']);
});

it('validates a page on create', function () {
    $this->postJson('api/pages', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['seitentitel']);
});

it('shows a page', function () {
    $page = Page::factory()->create();

    $this->getJson("api/pages/{$page->ID}")
        ->assertStatus(200)
        ->assertJson(['ID' => $page->ID]);
});

it('returns 404 for a missing page', function () {
    $this->getJson('api/pages/999999')->assertStatus(404);
});

it('updates a page', function () {
    $page = Page::factory()->create();

    $this->putJson("api/pages/{$page->ID}", ['seitentitel' => 'Renamed', 'inhalt' => 'x'])
        ->assertStatus(200)
        ->assertJson(['seitentitel' => 'Renamed']);
});

it('deletes a page', function () {
    $page = Page::factory()->create();

    $this->deleteJson("api/pages/{$page->ID}")->assertStatus(204);
    $this->assertDatabaseMissing('tbl_site', ['ID' => $page->ID]);
});

it('attaches a file to a page', function () {
    Storage::fake('attachments');
    $page = Page::factory()->create();

    $this->postJson("api/pages/{$page->ID}/attach", [
        'file' => UploadedFile::fake()->create('map.pdf', 100),
    ])->assertStatus(200);

    expect($page->fresh()->attachments)->toHaveCount(1);
});
