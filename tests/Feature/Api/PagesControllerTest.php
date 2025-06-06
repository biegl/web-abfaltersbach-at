<?php

use App\Models\Page;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('it returns a list of pages', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    Page::factory()->count(5)->create();

    $response = getJson('/api/pages');

    $response->assertSuccessful();
    $response->assertJsonCount(5);
});

test('it stores a new page', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $data = [
        'seitentitel' => 'This is a test page',
        'keywords' => 'test, page',
        'description' => 'This is a test page',
        'template' => 'default',
        'template_name' => 'default',
    ];

    $response = $this->postJson('/api/pages', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tbl_site', $data);
});

test('it shows a single page', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $page = Page::factory()->create();

    $response = getJson("/api/pages/{$page->ID}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['seitentitel' => $page->seitentitel]);
});

test('it updates a page', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $page = Page::factory()->create();

    $data = [
        'seitentitel' => 'This is an updated test page',
        'keywords' => 'test, page, updated',
        'description' => 'This is an updated test page',
        'template' => 'default',
        'template_name' => 'default',
    ];

    $response = $this->putJson("/api/pages/{$page->ID}", $data);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_site', $data);
});

test('it deletes a page', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $page = Page::factory()->create();

    $response = $this->deleteJson("/api/pages/{$page->ID}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tbl_site', ['ID' => $page->ID]);
});

test('it attaches a file to a page', function () {
    $user = User::factory()->admin()->create();
    actingAs($user, 'sanctum');

    $page = Page::factory()->create();
    Storage::fake('local');
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson("/api/pages/{$page->ID}/attach", [
        'file' => $file,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('tbl_downloads', [
        'attachable_id' => $page->ID,
        'attachable_type' => Page::class,
    ]);
}); 