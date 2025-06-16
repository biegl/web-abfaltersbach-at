<?php

use App\Models\News;

it('should report expiration status correctly', function () {
    $news1 = News::factory()->make(['expirationDate' => '2000-01-01']);
    $news2 = News::factory()->make(['expirationDate' => '2099-01-01']);
    $news3 = News::factory()->make(['expirationDate' => null]);

    expect($news1->is_expired)->toBeTrue();
    expect($news2->is_expired)->toBeFalse();
    expect($news3->is_expired)->toBeFalse();
});
