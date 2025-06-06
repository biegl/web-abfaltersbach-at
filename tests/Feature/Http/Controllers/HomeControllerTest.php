<?php

use App\Models\Event;
use App\Models\Navigation;
use App\Models\News;

test('home page can be rendered', function () {
    Event::factory()->create();
    News::factory()->create();
    Navigation::factory()->create();

    $response = $this->get('/');
    $response->assertOk();
    $response->assertViewIs('home');
    $response->assertViewHasAll(['navigation', 'news', 'grouped_events']);
    $this->assertInstanceOf(\Illuminate\View\View::class, $response->getOriginalContent());
});
