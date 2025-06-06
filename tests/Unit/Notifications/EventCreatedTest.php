<?php

use App\Models\Event;
use App\Notifications\EventCreated;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

test('does not send if event is in the past', function () {
    $event = Event::factory()->create(['date' => now()->subDay()]);
    $notification = new EventCreated($event);
    expect($notification->via($event))->toBe([]);
});

test('sends if event is in the future', function () {
    $event = Event::factory()->create(['date' => now()->addDay()]);
    $notification = new EventCreated($event);
    expect($notification->via($event))->toBe([TelegramChannel::class]);
});

test('returns telegram message when no image is present', function () {
    $event = Event::factory()->create(['text' => 'Some text without an image.']);
    $notification = new EventCreated($event);
    $message = $notification->toTelegram($event);

    expect($message)->toBeInstanceOf(TelegramMessage::class);
});

test('returns telegram file when image is present', function () {
    $event = Event::factory()->create(['text' => '<img src="https://example.com/image.jpg">']);
    $notification = new EventCreated($event);
    $message = $notification->toTelegram($event);
    $messageData = $message->toArray();

    expect($message)->toBeInstanceOf(TelegramFile::class);
    expect($messageData['photo'])->toBe('https://example.com/image.jpg');
});

test('telegram message has correct content', function () {
    $event = Event::factory()->create(['text' => '<p>Hello <b>World</b></p>']);
    $notification = new EventCreated($event);
    $message = $notification->toTelegram($event);
    $messageData = $message->toArray();

    $date = $event->date->toDateString();
    $title = "*Veranstaltung $date*";
    $content = "Hello **World**";

    expect($messageData['text'])->toBe(implode("\n", [$title, $content]));
});

test('telegram message has correct button', function () {
    $event = Event::factory()->create();
    $notification = new EventCreated($event);
    $message = $notification->toTelegram($event);
    $messageData = $message->toArray();
    $replyMarkup = json_decode($messageData['reply_markup'], true);

    $url = 'https://abfaltersbach.at?eventID='.$event->ID;
    expect($replyMarkup['inline_keyboard'][0][0]['url'])->toBe($url);
    expect($replyMarkup['inline_keyboard'][0][0]['text'])->toBe('Online ansehen');
});
