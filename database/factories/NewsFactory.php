<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\News;
use Faker\Generator as Faker;

$factory->define(News::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'text' => $faker->paragraph,
        'date' => now(),
        'expirationDate' => now()->addDays(rand(0, 365)),
    ];
});
