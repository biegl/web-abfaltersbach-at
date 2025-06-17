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
    protected $signature = 'telegram:test';

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
        $event = Event::first();
        $event->notify(new EventCreated($event));
    }
}
