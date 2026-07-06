<?php

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('downloads a file by name', function () {
    Storage::fake('attachments');

    // FilesController::storeFile() stores uploads at the root of the
    // 'attachments' disk via storeAs('', $fileName, 'attachments'), so the
    // File::file column holds a bare filename with no leading directory.
    // FilesController::download() calls Storage::download($file->file, ...)
    // using the default disk, which resolves to 'attachments' in this app
    // (config/filesystems.php: 'default' => env('FILESYSTEM_DRIVER', 'attachments')).
    $upload = UploadedFile::fake()->create('brochure.pdf', 50);
    $path = $upload->storeAs('', 'brochure.pdf', 'attachments');

    $file = File::factory()->create(['title' => 'brochure', 'file' => $path]);

    $this->get('/files/brochure')
        ->assertStatus(200)
        ->assertHeader('content-disposition');
});

it('returns 404 for an unknown download', function () {
    $this->get('/files/does-not-exist')->assertStatus(404);
});
