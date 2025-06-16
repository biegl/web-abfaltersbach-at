<?php

use App\Models\Event;
use App\Models\Navigation;
use App\Models\News;
use App\Models\Page;

test('home page can be rendered', function () {
    Event::factory()->create();
    News::factory()->create();
    $nav = Navigation::factory()->create(['linkname' => 'startseite', 'navianzeigen' => 'Ja']);
    $page = Page::factory()->create([
        'navigation_id' => $nav->ID,
        'template' => 'template_home.php',
    ]);

    $response = $this->get('/');
    $response->assertOk();
    $response->assertViewIs('page.home');
    $response->assertViewHasAll(['navigation', 'news', 'grouped_events']);
    $this->assertInstanceOf(\Illuminate\View\View::class, $response->getOriginalContent());
});
