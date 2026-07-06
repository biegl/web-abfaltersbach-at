<?php

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => asUser());

it('lists news', function () {
    News::factory()->count(3)->create();

    $this->getJson('api/news')
        ->assertStatus(200)
        ->assertJsonStructure(['data' => [['ID', 'title', 'text', 'date', 'expirationDate']]]);
});

it('creates news', function () {
    $this->postJson('api/news', [
        'title' => 'Hello',
        'text' => 'World',
        'date' => now()->format('Y-m-d'),
    ])
        ->assertStatus(201)
        ->assertJson(['title' => 'Hello', 'text' => 'World']);
});

it('validates news on create', function () {
    $this->postJson('api/news', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'text', 'date']);
});

it('shows a news item', function () {
    $news = News::factory()->create();

    $this->getJson("api/news/{$news->ID}")
        ->assertStatus(200)
        ->assertJson(['ID' => $news->ID]);
});

it('returns 404 for a missing news item', function () {
    $this->getJson('api/news/999999')->assertStatus(404);
});

it('updates news', function () {
    $news = News::factory()->create();

    $this->putJson("api/news/{$news->ID}", [
        'title' => 'Updated',
        'text' => 'Body',
        'date' => now()->format('Y-m-d'),
    ])
        ->assertStatus(200)
        ->assertJson(['title' => 'Updated']);
});

it('validates news on update', function () {
    $news = News::factory()->create();

    $this->putJson("api/news/{$news->ID}", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'text', 'date']);
});

it('deletes news', function () {
    $news = News::factory()->create();

    $this->deleteJson("api/news/{$news->ID}")->assertStatus(204);
    $this->assertDatabaseMissing('tbl_news', ['ID' => $news->ID]);
});

it('attaches a file to news', function () {
    Storage::fake('attachments');
    $news = News::factory()->create();

    $this->postJson("api/news/{$news->ID}/attach", [
        'file' => UploadedFile::fake()->create('doc.pdf', 100),
    ])->assertStatus(200);

    expect($news->fresh()->attachments)->toHaveCount(1);
});
