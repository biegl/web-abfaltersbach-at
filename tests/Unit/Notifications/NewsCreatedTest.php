<?php

use App\Models\News;
use App\Notifications\NewsCreated;

test('does not send if news is expired', function () {
    $news = News::factory()->create(['expirationDate' => now()->subDay()]);
    $notification = new NewsCreated($news);
    expect($notification->via($news))->toBe([]);
});

test('sends if news is not expired', function () {
    $news = News::factory()->create(['expirationDate' => now()->addDay()]);
    $notification = new NewsCreated($news);
    expect($notification->via($news))->toBe(['telegram']);
});

test('returns a telegram message', function () {
    $news = News::factory()->create(['text' => 'Some text.']);
    $notification = new NewsCreated($news);
    $message = $notification->toTelegram($news);

    expect($message)->toBeArray()
        ->and($message)->toHaveKey('text');
});

test('telegram message has correct content', function () {
    $news = News::factory()->create([
        'title' => 'Test Title',
        'text' => '<p>Hello <b>World</b></p>',
    ]);
    $notification = new NewsCreated($news);
    $message = $notification->toTelegram($news);

    $title = '*Test Title*';
    $content = 'Hello **World**';

    expect($message['text'])->toBe(implode("\n", [$title, $content]));
});

test('telegram message has correct button', function () {
    $news = News::factory()->create();
    $notification = new NewsCreated($news);
    $message = $notification->toTelegram($news);
    $replyMarkup = json_decode($message['reply_markup'], true);

    $url = 'https://abfaltersbach.at?newsID='.$news->ID;
    expect($replyMarkup['inline_keyboard'][0][0]['url'])->toBe($url);
    expect($replyMarkup['inline_keyboard'][0][0]['text'])->toBe('Online ansehen');
});
