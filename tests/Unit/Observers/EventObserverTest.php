<?php

use App\Models\Event;
use App\Models\File;
use App\Notifications\EventCreated;
use App\Observers\EventObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

test('sends notification on event creation', function () {
    Notification::fake();
    $event = Event::factory()->create();
    $observer = new EventObserver();

    $observer->created($event);

    Notification::assertSentTo($event, EventCreated::class);
});

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
