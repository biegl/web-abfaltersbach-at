<?php

use App\Models\File;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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