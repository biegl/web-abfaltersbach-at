<?php

use App\Models\Event;
use App\Models\File;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('it returns a list of files', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    File::factory()->count(5)->create();

    $response = getJson('/api/files');

    $response->assertSuccessful();
    $response->assertJsonCount(5);
});

test('it stores a new file', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    Storage::fake('local');
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson('/api/files', [
        'file' => $file,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tbl_downloads', [
        'title' => 'test.jpg',
    ]);
});

test('it shows a single file', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $file = File::factory()->create();

    $response = getJson("/api/files/{$file->ID}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['title' => $file->title]);
});

test('it updates a file', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $file = File::factory()->create();

    $data = [
        'title' => 'This is an updated test file',
    ];

    $response = $this->putJson("/api/files/{$file->ID}", $data);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_downloads', $data);
});

test('it deletes a file', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $file = File::factory()->create();

    $response = $this->deleteJson("/api/files/{$file->ID}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tbl_downloads', ['ID' => $file->ID]);
});

test('it deletes a file attached to an event and clears cache', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $event = Event::factory()->create();
    $file = File::factory()->create([
        'attachable_id' => $event->ID,
        'attachable_type' => Event::class,
    ]);

    Cache::shouldReceive('forget')->with(Event::$CACHE_KEY_CURRENT_EVENTS)->once();
    Cache::shouldReceive('forget')->with(Event::$CACHE_KEY_GROUPED_EVENTS)->once();

    $response = $this->deleteJson("/api/files/{$file->ID}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tbl_downloads', ['ID' => $file->ID]);
});

test('it deletes a file attached to a news and clears cache', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $news = News::factory()->create();
    $file = File::factory()->create([
        'attachable_id' => $news->ID,
        'attachable_type' => News::class,
    ]);

    Cache::shouldReceive('forget')->with(News::$CACHE_KEY_TOP_NEWS)->once();

    $response = $this->deleteJson("/api/files/{$file->ID}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tbl_downloads', ['ID' => $file->ID]);
});

test('storeFile returns null if no file is present', function () {
    $controller = new \App\Http\Controllers\Api\FilesController;
    $request = new \Illuminate\Http\Request;

    $result = $controller->storeFile($request);

    expect($result)->toBeNull();
});
