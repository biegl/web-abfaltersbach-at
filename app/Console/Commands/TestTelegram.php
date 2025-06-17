<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\News;
use App\Notifications\EventCreated;
use App\Notifications\NewsCreated;
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
    protected $description = 'Send a test message to the Telegram channel for an event or news item';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->choice(
            'What type of notification do you want to send?',
            ['event', 'news'],
            0
        );
        
        // Get available IDs based on type
        $availableIds = $type === 'event' 
            ? Event::pluck('id')->toArray()
            : News::pluck('id')->toArray();

        if (empty($availableIds)) {
            $this->error("No {$type}s found in the database.");
            return 1;
        }

        $id = $this->anticipate(
            "Enter the {$type} ID",
            $availableIds,
            $availableIds[0]
        );

        if (!in_array($id, $availableIds)) {
            $this->error("{$type} with ID {$id} not found.");
            return 1;
        }

        if ($type === 'event') {
            $model = Event::find($id);
            $model->notify(new EventCreated($model));
        } else {
            $model = News::find($id);
            $model->notify(new NewsCreated($model));
        }

        $this->info("Test notification sent for {$type} with ID: {$id}");
        return 0;
    }
}
