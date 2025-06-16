<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramService
{
    /**
     * @var Api
     */
    protected $telegram;

    protected string $defaultChannel;

    protected int $maxRetries = 3;
    protected int $retryDelay = 1; // seconds

    public function __construct()
    {
        if (config('telegram.bot_token') && config('telegram.default_channel')) {
            $this->telegram = new Api(config('telegram.bot_token', ''));
            $this->defaultChannel = config('telegram.default_channel', '');
        }
    }

    /**
     * @throws \Exception
     */
    public function send(mixed $message): void
    {
        Log::info('Sending telegram message', is_array($message) ? $message : []);

        if (is_string($message)) {
            $this->sendTextMessage($message);
        } elseif (is_array($message)) {
            $this->sendMessageFromArray($message);
        } else {
            throw new \Exception('Telegram notification message format not supported.');
        }
    }

    /**
     * @throws TelegramSDKException
     */
    protected function sendTextMessage(string $message): void
    {
        $this->sendWithRetry(function () use ($message) {
            $this->telegram->sendMessage([
                'chat_id' => $this->defaultChannel,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        });
    }

    /**
     * @throws TelegramSDKException
     */
    protected function sendMessageFromArray(array $message): void
    {
        $params = array_merge([
            'chat_id' => $this->defaultChannel,
            'parse_mode' => 'Markdown',
        ], $message);

        $this->sendWithRetry(function () use ($params, $message) {
            if (isset($message['photo'])) {
                $this->telegram->sendPhoto($params);
            } else {
                $this->telegram->sendMessage($params);
            }
        });
    }

    /**
     * Execute a Telegram API call with retry logic
     *
     * @param callable $callback
     * @throws TelegramSDKException
     */
    protected function sendWithRetry(callable $callback): void
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < $this->maxRetries) {
            try {
                $callback();
                return;
            } catch (\Telegram\Bot\Exceptions\TelegramResponseException $e) {
                $lastException = $e;
                
                // Check if it's a rate limit error
                if (str_contains($e->getMessage(), 'Too Many Requests')) {
                    $attempts++;
                    if ($attempts < $this->maxRetries) {
                        // Extract retry delay from error message if available
                        if (preg_match('/retry after (\d+)/', $e->getMessage(), $matches)) {
                            $retryDelay = (int) $matches[1];
                        } else {
                            $retryDelay = $this->retryDelay * $attempts;
                        }
                        
                        sleep($retryDelay);
                        continue;
                    }
                }
                
                // If it's not a rate limit error or we've exhausted retries, rethrow
                throw $e;
            }
        }

        // If we've exhausted all retries, throw the last exception
        if ($lastException) {
            throw $lastException;
        }
    }
}
