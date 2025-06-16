<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;

class SendTelegramMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:send
                            {--title= : The title of the message}
                            {--image= : URL of an image to send with the message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message to Telegram';

    /**
     * @var TelegramService
     */
    protected $telegramService;

    /**
     * Create a new command instance.
     */
    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle(): int
    {
        $title = $this->option('title') ?: $this->ask('Enter a title for the message');
        $message = $this->ask('Enter your message');
        $imageUrl = $this->option('image') ?: $this->ask('Enter an image URL (optional, press enter to skip)', null);

        if (empty($message)) {
            $this->error('Message cannot be empty!');

            return 1;
        }

        $this->info('Sending message to Telegram...');

        try {
            $this->sendToTelegram($title, $message, $imageUrl);
            $this->info('Message sent successfully!');

            return 0;
        } catch (TelegramSDKException $e) {
            $this->error('Failed to send message: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * @throws TelegramSDKException
     */
    protected function sendToTelegram(string $title, string $message, ?string $imageUrl = null): void
    {
        $formattedMessage = "*{$title}*\n\n{$message}";

        if ($imageUrl) {
            $this->telegramService->send([
                'photo' => $imageUrl,
                'caption' => $formattedMessage,
            ]);
        } else {
            $this->telegramService->send($formattedMessage);
        }
    }
}
