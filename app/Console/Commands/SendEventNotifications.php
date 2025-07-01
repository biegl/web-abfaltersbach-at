<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

class SendEventNotifications extends Command
{
    protected $signature = 'events:send-notifications';

    protected $description = 'Send Telegram notifications for today\'s events';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // Get all events for today that haven't been notified yet
        $events = Event::query()
            ->whereDate('date', $today)
            ->whereNull('notification_sent_at')
            ->get();

        if ($events->isEmpty()) {
            $this->info('No new events to notify for today.');

            return;
        }

        foreach ($events as $event) {
            $this->sendTelegramNotification($event);

            // Mark the event as notified
            $event->update([
                'notification_sent_at' => Carbon::now(),
            ]);
        }

        $this->info('Event notifications sent successfully.');
    }

    protected function sendTelegramNotification(Event $event)
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => 'img',
        ]);

        $date = $event->date->toDateString();
        $title = "*Veranstaltung $date*";

        // Check if content contains image
        preg_match('@src="([^"]+)"@', $event->text, $match);

        // Convert content to markdown and remove images
        $content = $converter->convert($event->text);
        $url = 'https://abfaltersbach.at?eventID='.$event->ID;

        if ($src = array_pop($match)) {
            TelegramFile::create()
                ->to(config('services.telegram-bot-api.channel'))
                ->content(implode("\n", [$title, $content]))
                ->photo($src)
                ->button('Online ansehen', $url)
                ->send();
        } else {
            TelegramMessage::create()
                ->to(config('services.telegram-bot-api.channel'))
                ->content(implode("\n", [$title, $content]))
                ->button('Online ansehen', $url)
                ->send();
        }
    }
}
