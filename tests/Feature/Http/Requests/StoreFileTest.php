<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

test('file is required', function () {
    $this->postJson('/_test/store-file', [])
        ->assertJsonValidationErrors('file');
});

test('file must be a file', function () {
    $this->postJson('/_test/store-file', ['file' => 'not-a-file'])
        ->assertJsonValidationErrors('file');
});

test('passes with valid file', function () {
    $file = UploadedFile::fake()->image('test.jpg');

    $this->postJson('/_test/store-file', ['file' => $file])
        ->assertOk();
});
