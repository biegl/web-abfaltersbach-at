<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramNotificationChannel
{
    /**
     * @var Api
     */
    protected $telegram;

    /**
     * @var string
     */
    protected $defaultChannel;

    /**
     * Create a new Telegram notification channel instance.
     *
     * @param Api $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        $this->defaultChannel = config('telegram.default_channel');
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     * @throws TelegramSDKException
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toTelegram')) {
            throw new \Exception('Notification does not have toTelegram method.');
        }

        $message = $notification->toTelegram($notifiable);
        
        if (is_string($message)) {
            $this->sendTextMessage($message);
        } elseif (is_array($message)) {
            $this->sendMessageFromArray($message);
        } else {
            throw new \Exception('Telegram notification message format not supported.');
        }
    }

    /**
     * Send a text message.
     *
     * @param string $message
     * @return void
     * @throws TelegramSDKException
     */
    protected function sendTextMessage($message)
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->defaultChannel,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }

    /**
     * Send a message from an array.
     *
     * @param array $message
     * @return void
     * @throws TelegramSDKException
     */
    protected function sendMessageFromArray(array $message)
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