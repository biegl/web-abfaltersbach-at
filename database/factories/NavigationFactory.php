<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Navigation;
use Faker\Generator as Faker;

$factory->define(Navigation::class, function (Faker $faker) {
    return [
        'position' => $faker->numberBetween(0, 10),
        'level' => $faker->numberBetween(0, 1),
        'name' => $faker->text(10),
        'linkname' => $faker->slug(),
        'navianzeigen' => 'Ja',
    ];
});
