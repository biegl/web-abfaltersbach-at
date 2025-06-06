<?php

use App\Models\Event;
use App\Models\Navigation;
use App\Models\News;

test('home page can be rendered', function () {
    Event::factory()->create();
    News::factory()->create();
    Navigation::factory()->create();

    $this->get('/')->assertOk()->assertViewIs('home')->assertViewHasAll(['navigation', 'news', 'grouped_events']);
});
