<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

class NewsCreated extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $news;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(News $news)
    {
        $this->news = $news;
        $this->delay($news->date);
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

        $title = "*$notifiable->title*";

        // Check if content contains image
        preg_match('@src="([^"]+)"@', $notifiable->text, $match);

        // Convert content to markdown and remove images
        $content = $converter->convert($notifiable->text);

        if ($src = array_pop($match)) {
            return TelegramFile::create()
                ->to(env('TELEGRAM_CHANNEL_NAME', '@abfaltersbach'))
                ->content(join("\n", [$title, $content]))
                ->photo($src)
                ->button('Online ansehen', 'https://abfaltersbach.at');
        } else {
            return TelegramMessage::create()
                ->to(env('TELEGRAM_CHANNEL_NAME', '@abfaltersbach'))
                ->content(join("\n", [$title, $content]))
                ->button('Online ansehen', 'https://abfaltersbach.at');
        }
    }
}
