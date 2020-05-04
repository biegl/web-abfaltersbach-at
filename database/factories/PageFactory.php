<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Page;
use Faker\Generator as Faker;

$factory->define(Page::class, function (Faker $faker) {
    return [
        'seitentitel' => $faker->text(20),
        'keywords' => $faker->text(10),
        'template' => $faker->text(50),
        'template_name' => $faker->text(45),
        'inhalt' => $faker->sentence(50),
        'description' => $faker->sentence(20),
    ];
});
