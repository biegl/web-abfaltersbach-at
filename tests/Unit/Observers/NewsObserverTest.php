<?php

use App\Models\File;
use App\Models\News;
use App\Observers\NewsObserver;
use Illuminate\Support\Facades\DB;

test('deletes attachments on news deletion', function () {
    $news = News::factory()->create();
    $attachment = File::factory()->create([
        'attachable_id' => $news->ID,
        'attachable_type' => News::class,
    ]);
    $observer = new NewsObserver;

    $news->refresh();
    expect($news->attachments)->toHaveCount(1);
    $attachmentId = $attachment->ID;

    $observer->deleted($news);

    $attachmentExists = DB::table('tbl_downloads')->where('ID', $attachmentId)->exists();
    expect($attachmentExists)->toBeFalse();
});
