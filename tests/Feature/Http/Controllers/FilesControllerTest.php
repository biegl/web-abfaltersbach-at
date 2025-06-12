<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it downloads a file', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $this->actingAs($user);

    $fakeFile = UploadedFile::fake()->image('test.jpg');
    $filename = $fakeFile->hashName();

    $file = File::factory()->create([
        'file' => 'files/'.$filename,
    ]);

    Storage::disk('local')->put('files/'.$filename, $fakeFile->get());

    $response = $this->get(route('download', ['name' => $file->title]));

    $response->assertSuccessful();
    $response->assertHeader('content-disposition', 'attachment; filename="'.$file->title.'"');
});
