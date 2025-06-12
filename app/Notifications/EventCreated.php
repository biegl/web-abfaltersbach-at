<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use League\HTMLToMarkdown\HtmlConverter;

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

        return ['telegram'];
    }

    public function dontSend($notifiable)
    {
        return is_null($notifiable) || $notifiable->date->isPast();
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

        $message = [
            'text' => implode("\n", [$title, $content]),
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Online ansehen',
                            'url' => $url
                        ]
                    ]
                ]
            ])
        ];

        if ($src = array_pop($match)) {
            $message['photo'] = $src;
        }

        return $message;
    }
}
