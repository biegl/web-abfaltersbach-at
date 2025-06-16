<?php

use App\Http\Controllers\ListController;
use App\Models\News;

test('list controller returns items in correct order', function () {
    $news1 = News::factory()->create();
    $news2 = News::factory()->create();
    $news3 = News::factory()->create();

    $ids = [$news2->ID, $news3->ID, $news1->ID];

    $items = ListController::getItems(1, ['model' => News::class, 'ids' => $ids]);

    expect($items->count())->toBe(3);
    expect($items[0]->ID)->toBe($news2->ID);
    expect($items[1]->ID)->toBe($news3->ID);
    expect($items[2]->ID)->toBe($news1->ID);
});

test('list controller returns empty array when no ids are provided', function () {
    $items = ListController::getItems(1, ['model' => News::class, 'ids' => []]);

    expect($items)->toBe([]);
});
