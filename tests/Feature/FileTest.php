<?php

use App\Models\File;
use App\Models\Navigation;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a file record in the database', function () {
    // Act
    File::factory()->create(['title' => 'Testfile']);
    
    // Assert
    expect(File::where('title', 'Testfile')->exists())->toBeTrue();
});

test('it lists all attached files for a specific page', function () {
    // Arrange
    $page = Page::factory()->create(['navigation_id' => 1]);
    $navItem = Navigation::factory()->create(['ID' => $page->ID]);

    $files = File::factory(5)->create([
        'navID' => $navItem->ID,
    ]);

    $otherFile = File::factory()->create([
        'navID' => $navItem->ID + 1,
    ]);

    // Act & Assert
    expect(File::count())->toBe(6)
        ->and($page->files)->toHaveCount(5);
});
