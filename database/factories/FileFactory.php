<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\File;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(File::class, function (Faker $faker) {
    $fakeFile = UploadedFile::fake()->create($faker->word() . '.pdf', 1000);

    return [
        'navID' => $faker->numberBetween(0, 255),
        'position' => $faker->numberBetween(0, 10),
        'title' => $faker->sentence(4),
        'file' => '/uploads/test-files/' . $fakeFile->name,
    ];
});
