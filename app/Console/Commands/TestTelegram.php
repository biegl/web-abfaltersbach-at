<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventCreated;
use Illuminate\Console\Command;

class TestTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test {event_id? : The ID of the event to send. If not provided, the first event will be used.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test message to the Telegram channel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eventId = $this->argument('event_id');
        
        $event = $eventId 
            ? Event::find($eventId)
            : Event::first();

        if (!$event) {
            $this->error('No event found.');
            return 1;
        }

        $event->notify(new EventCreated($event));
        $this->info("Test notification sent for event: {$eventId}");
        
        return 0;
    }
}
