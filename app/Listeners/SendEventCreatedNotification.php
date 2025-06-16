<?php

namespace App\Listeners;

use App\Events\EventCreated;
use App\Models\Event;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use League\HTMLToMarkdown\HtmlConverter;

class SendEventCreatedNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected TelegramService $telegramService) {}

    public function handle(EventCreated $event): void
    {
        if (! $this->shouldSend($event->event)) {
            return;
        }

        $message = $this->buildMessage($event->event);
        $this->telegramService->send($message);
    }

    private function shouldSend(Event $event): bool
    {
        return true;
    }

    private function buildMessage(Event $event): array
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

        $message = [
            'text' => implode("\n", [$title, $content]),
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Online ansehen',
                            'url' => $url,
                        ],
                    ],
                ],
            ]),
        ];

        if ($src = array_pop($match)) {
            $message['photo'] = $src;
        }

        return $message;
    }
}
