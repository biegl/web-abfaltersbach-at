<?php

use App\Models\Event;
use App\Models\File;
use App\Observers\EventObserver;
use Illuminate\Support\Facades\DB;

test('deletes attachments on event deletion', function () {
    $event = Event::factory()->create();
    $attachment = File::factory()->create([
        'attachable_id' => $event->ID,
        'attachable_type' => Event::class,
    ]);
    $observer = new EventObserver();

    $event->refresh();
    expect($event->attachments)->toHaveCount(1);
    $attachmentId = $attachment->ID;

    $observer->deleted($event);

    $attachmentExists = DB::table('tbl_downloads')->where('ID', $attachmentId)->exists();
    expect($attachmentExists)->toBeFalse();
});
