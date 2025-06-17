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
    protected $signature = 'telegram:test {id : The ID of the event or news item}';

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
        
        $id = $this->argument('id');

        if ($type === 'event') {
            $model = Event::find($id);
            if (!$model) {
                $this->error("Event with ID {$id} not found.");
                return 1;
            }
            $model->notify(new EventCreated($model));
        } else {
            $model = News::find($id);
            if (!$model) {
                $this->error("News with ID {$id} not found.");
                return 1;
            }
            $model->notify(new NewsCreated($model));
        }

        $this->info("Test notification sent for {$type} with ID: {$id}");
        return 0;
    }
}
