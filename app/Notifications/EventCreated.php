<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

class EventCreated extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->delay($event->date);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->dontSend($notifiable)) {
            return [];
        }

        return [TelegramChannel::class];
    }

    public function dontSend($notifiable)
    {
        return is_null($notifiable) || $notifiable->isExpired;
    }

    public function toTelegram($notifiable)
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => 'img',
        ]);

        $date = $notifiable->date->toDateString();
        $title = "*Veranstaltung $date*";

        // Check if content contains image
        preg_match('@src="([^"]+)"@', $notifiable->text, $match);

        // Convert content to markdown and remove images
        $content = $converter->convert($notifiable->text);
        $url = 'https://abfaltersbach.at?eventID='.$notifiable->ID;

        if ($src = array_pop($match)) {
            return TelegramFile::create()
                ->to(env('TELEGRAM_CHANNEL_NAME', '@abfaltersbach'))
                ->content(implode("\n", [$title, $content]))
                ->photo($src)
                ->button('Online ansehen', $url);
        } else {
            return TelegramMessage::create()
                ->to(env('TELEGRAM_CHANNEL_NAME', '@abfaltersbach'))
                ->content(implode("\n", [$title, $content]))
                ->button('Online ansehen', $url);
        }
    }
}
