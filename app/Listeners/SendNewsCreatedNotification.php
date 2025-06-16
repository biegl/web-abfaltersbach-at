<?php

namespace App\Listeners;

use App\Events\NewsCreated;
use App\Models\News;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use League\HTMLToMarkdown\HtmlConverter;

class SendNewsCreatedNotification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected TelegramService $telegramService)
    {
    }

    public function handle(NewsCreated $event): void
    {
        if (! $this->shouldSend($event->news)) {
            return;
        }

        $this->delay($event->news->date);

        $message = $this->buildMessage($event->news);
        $this->telegramService->send($message);
    }

    private function shouldSend(News $news): bool
    {
        return true;
    }

    private function buildMessage(News $news): array
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => 'img',
        ]);

        $title = "*$news->title*";

        // Convert content to markdown and remove images
        $content = $converter->convert($news->text);

        $url = "https://abfaltersbach.at?newsID={$news->ID}";

        return [
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
    }
}
