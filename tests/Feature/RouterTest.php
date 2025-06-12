<?php

namespace Tests\Feature;

use App\Models\Navigation;
use App\Models\Page;
use App\Router\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->router = new Router;
});

afterEach(function () {
    $this->router->clearCache();
});

test('it finds a page by url', function () {
    // Arrange
    $navOne = Navigation::factory()->create([
        'refID' => null,
        'navianzeigen' => 'Ja',
        'linkname' => 'test-page',
    ]);
    $navTwo = Navigation::factory()->create([
        'refID' => null,
        'navianzeigen' => 'Ja',
        'linkname' => 'another-page',
    ]);

    $pageOne = Page::factory()->create(['navigation_id' => $navOne->ID]);
    $pageTwo = Page::factory()->create(['navigation_id' => $navTwo->ID]);

    // Act
    $page = $this->router->findByUrl('/test-page');

    // Assert
    expect($page)->not->toBeNull()
        ->and($page->content)->toBe($pageOne->content);
});

test('it does not find a non existing page', function () {
    // Arrange
    $navOne = Navigation::factory()->create([
        'refID' => null,
        'navianzeigen' => 'Ja',
        'linkname' => 'nav-one',
    ]);
    $navTwo = Navigation::factory()->create([
        'refID' => null,
        'navianzeigen' => 'Ja',
        'linkname' => 'nav-two',
    ]);

    $pageOne = Page::factory()->create(['navigation_id' => $navOne->ID]);
    $pageTwo = Page::factory()->create(['navigation_id' => $navTwo->ID]);

    // Act
    $page = $this->router->findByUrl('foo-bar');

    // Assert
    expect($page)->toBeNull();
});
