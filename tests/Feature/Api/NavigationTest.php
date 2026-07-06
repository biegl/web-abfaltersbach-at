<?php

use App\Models\Navigation;

beforeEach(fn () => asUser());

it('lists navigation', function () {
    Navigation::factory()->count(2)->create(['level' => 0]);

    $this->getJson('api/navigation')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

it('includes hidden navigation with showAll', function () {
    $this->getJson('api/navigation?showAll=1')->assertStatus(200);
});
