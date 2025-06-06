<?php

use App\Models\News;
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

test('it deletes a news article with attachments', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    Storage::fake('local');
    $news = News::factory()->create();
    $file = UploadedFile::fake()->create('test.jpg');

    // Mock the FilesController
    $fileControllerMock = $this->mock(\App\Http\Controllers\Api\FilesController::class)->makePartial();
    $fileModel = new \App\Models\File(['file' => 'test.jpg', 'title' => 'test.jpg']);
    $fileControllerMock->shouldReceive('storeFile')->andReturn($fileModel);

    // Attach the file
    $request = new \Illuminate\Http\Request();
    $request->files->set('file', $file);
    $fileRecord = $fileControllerMock->storeFile($request);
    $news->attachments()->save($fileRecord);

    Storage::disk('local')->put('test.jpg', 'test');
    Storage::disk('local')->assertExists($fileRecord->file);

    // Delete the news
    $response = $this->deleteJson("/api/news/{$news->ID}");
    $response->assertStatus(204);

    // Assertions
    $this->assertDatabaseMissing('tbl_news', ['ID' => $news->ID]);
    $this->assertDatabaseMissing('tbl_downloads', ['ID' => $fileRecord->ID]);
    Storage::disk('local')->assertMissing($fileRecord->file);
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
