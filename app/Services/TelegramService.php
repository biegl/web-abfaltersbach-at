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

    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bot_token'));
        $this->defaultChannel = config('telegram.default_channel');
    }

    /**
     * @throws \Exception
     */
    public function send(mixed $message): void
    {
        Log::info('Sending telegram message', $message);

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
        $this->telegram->sendMessage([
            'chat_id' => $this->defaultChannel,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
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

        if (isset($message['photo'])) {
            $this->telegram->sendPhoto($params);
        } else {
            $this->telegram->sendMessage($params);
        }
    }
}
