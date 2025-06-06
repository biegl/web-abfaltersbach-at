<?php

use App\Models\News;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('it returns a list of news', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    News::factory()->count(5)->create();

    $response = getJson('/api/news');

    $response->assertSuccessful();
    $response->assertJsonCount(5, 'data');
});

test('it returns all news when showAll is passed', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    News::factory()->count(5)->create(['expirationDate' => now()->subDay()]);
    News::factory()->count(5)->create();

    $response = getJson('/api/news?showAll=all');

    $response->assertSuccessful();
    $response->assertJsonCount(10, 'data');
});

test('it stores a new news article', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $data = [
        'title' => 'This is a test news article',
        'text' => 'This is the text of the test news article.',
        'date' => now()->format('Y-m-d'),
    ];

    $response = postJson('/api/news', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tbl_news', $data);
});

test('it shows a single news article', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $news = News::factory()->create();

    $response = getJson("/api/news/{$news->ID}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['title' => $news->title]);
});

test('it updates a news article', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $news = News::factory()->create();

    $data = [
        'title' => 'This is an updated title',
        'text' => 'This is the updated text.',
        'date' => now()->format('Y-m-d'),
    ];

    $response = putJson("/api/news/{$news->ID}", $data);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_news', $data);
});

test('it deletes a news article', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $news = News::factory()->create();

    $response = deleteJson("/api/news/{$news->ID}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tbl_news', ['id' => $news->ID]);
});

test('it attaches a file to a news article', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $news = News::factory()->create();
    Storage::fake('local');
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson("/api/news/{$news->ID}/attach", [
        'file' => $file,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_downloads', [
        'attachable_id' => $news->ID,
        'attachable_type' => News::class,
    ]);
}); 