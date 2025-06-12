<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use League\HTMLToMarkdown\HtmlConverter;

class NewsCreated extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

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

        return ['telegram'];
    }

    public function dontSend($notifiable): bool
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

        // Convert content to markdown and remove images
        $content = $converter->convert($notifiable->text);

        $url = "https://abfaltersbach.at?newsID={$notifiable->ID}";

        return [
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
    }
}
