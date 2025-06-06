<?php

use App\Models\Navigation;
use App\Models\Page;
use App\Router\Router;

test('landing page redirects to home', function () {
    $this->get('/startseite')
         ->assertRedirect('/');
});

test('shows page if found', function () {
    $root = Navigation::factory()->create(['refID' => null]);
    $navigation = Navigation::factory()->create(['refID' => $root->ID]);
    $page = Page::factory()->create(['navigation_id' => $navigation->ID]);
    Navigation::factory()->create(['linkname' => 'startseite', 'refID' => null]);

    $this->mock(Router::class, function ($mock) use ($page) {
        $mock->shouldReceive('findByUrl')->andReturn($page);
    });

    $this->get('/a-random-url')
         ->assertOk()
         ->assertViewIs($page->templateName);
});

test('shows 404 if page not found', function () {
    $this->mock(Router::class, function ($mock) {
        $mock->shouldReceive('findByUrl')->andReturn(null);
    });

    $this->get('/non-existent-page')
         ->assertOk()
         ->assertViewIs('errors.404');
});
