<?php

namespace App\Observers;

use App\Events\EventCreated;
use App\Models\Event;

class EventObserver
{
    public function created(Event $event): void
    {
        EventCreated::dispatch($event);
    }

    public function deleted(Event $event): void
    {
        $event->attachments()->delete();
    }
}
