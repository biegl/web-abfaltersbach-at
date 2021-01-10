<?php

use App\News;

use function PHPUnit\Framework\assertFalse;

it('should report expiration status correctly', function () {
    $news1 = factory(News::class)->make(['expirationDate' => '2000-01-01']);
    $news2 = factory(News::class)->make(['expirationDate' => '2099-01-01']);
    $news3 = factory(News::class)->make(['expirationDate' => null]);

    assertTrue($news1->isExpired);
    assertFalse($news2->isExpired);
    assertFalse($news3->isExpired);
});
