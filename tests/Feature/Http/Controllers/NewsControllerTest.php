<?php

use App\Models\News;

test('can show news index', function () {
    News::factory()->count(3)->create();

    $this->get(route('news.index'))
         ->assertOk()
         ->assertViewIs('news.index')
         ->assertViewHas('news');
});
