<?php

use App\Models\News;
use App\Models\User;

test('can show news index', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    News::factory()->count(3)->create();

    $this->get(route('news.index'))
        ->assertOk()
        ->assertViewIs('news.index')
        ->assertViewHas('news');
});
