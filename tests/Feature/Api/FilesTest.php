<?php

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('attachments');
    asUser();
});

it('lists files', function () {
    File::factory()->count(2)->create();

    $this->getJson('api/files')->assertStatus(200);
});

it('stores an uploaded file', function () {
    $this->postJson('api/files', [
        'file' => UploadedFile::fake()->create('report.pdf', 120),
    ])->assertStatus(201);

    expect(File::count())->toBeGreaterThan(0);
});

it('shows a file', function () {
    $file = File::factory()->create();

    $this->getJson("api/files/{$file->ID}")
        ->assertStatus(200)
        ->assertJson(['ID' => $file->ID]);
});

it('returns 404 for a missing file', function () {
    $this->getJson('api/files/999999')->assertStatus(404);
});

it('updates a file title', function () {
    $file = File::factory()->create();

    $this->putJson("api/files/{$file->ID}", ['title' => 'New title'])
        ->assertStatus(200)
        ->assertJson(['title' => 'New title']);
});

it('validates a file title on update', function () {
    $file = File::factory()->create();

    $this->putJson("api/files/{$file->ID}", ['title' => ''])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

it('deletes a file', function () {
    $file = File::factory()->create();

    $this->deleteJson("api/files/{$file->ID}")->assertStatus(204);
    $this->assertDatabaseMissing('tbl_downloads', ['ID' => $file->ID]);
});
