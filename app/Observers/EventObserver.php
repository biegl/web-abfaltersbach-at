<?php

namespace App\Observers;

use App\Models\Event;
use App\Notifications\EventCreated;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     *
     * @param  \App\Models\Event  $Event
     * @return void
     */
    public function created(Event $event)
    {
        $event->notify(new EventCreated($event));
    }

    /**
     * Handle the Event "deleted" event.
     *
     * @param  \App\Models\Event  $Event
     * @return void
     */
    public function deleted(Event $event)
    {
        $event->attachments()->delete();
    }
}
