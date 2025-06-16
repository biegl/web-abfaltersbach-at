<?php

use App\Console\Commands\SendTelegramMessage;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;

beforeEach(function () {
    config(['telegram.bot_token' => 'dummy', 'telegram.default_channel' => 'dummy_channel']);
    $this->called = false;
    $this->telegramServiceMock = Mockery::mock(TelegramService::class);
    $this->app->instance(TelegramService::class, $this->telegramServiceMock);
});

test('it sends text message to telegram', function () {
    $this->markTestSkipped('Skipping Telegram API test');
    $this->telegramServiceMock->shouldReceive('send')
        ->once()
        ->with("*Test Title*\n\nThis is a test message")
        ->andReturnUsing(function () { $this->called = true; return null; });
    $this->artisan('telegram:send')
        ->expectsQuestion('Enter a title for the message', 'Test Title')
        ->expectsQuestion('Enter your message', 'This is a test message')
        ->expectsQuestion('Enter an image URL (optional, press enter to skip)', null)
        ->expectsOutput('Sending message to Telegram...')
        ->expectsOutput('Message sent successfully!')
        ->assertExitCode(0);
    expect($this->called)->toBeTrue();
});

test('it sends photo message to telegram', function () {
    $this->markTestSkipped('Skipping Telegram API test');
    $this->telegramServiceMock->shouldReceive('send')
        ->once()
        ->with([
            'photo' => 'https://example.com/image.jpg',
            'caption' => "*Test Title*\n\nThis is a test message with image",
        ])
        ->andReturnUsing(function () { $this->called = true; return null; });
    $this->artisan('telegram:send')
        ->expectsQuestion('Enter a title for the message', 'Test Title')
        ->expectsQuestion('Enter your message', 'This is a test message with image')
        ->expectsQuestion('Enter an image URL (optional, press enter to skip)', 'https://example.com/image.jpg')
        ->expectsOutput('Sending message to Telegram...')
        ->expectsOutput('Message sent successfully!')
        ->assertExitCode(0);
    expect($this->called)->toBeTrue();
});

test('it handles empty message', function () {
    $this->markTestSkipped('Skipping Telegram API test');
    $this->artisan('telegram:send')
        ->expectsQuestion('Enter a title for the message', 'Test Title')
        ->expectsQuestion('Enter your message', '')
        ->expectsQuestion('Enter an image URL (optional, press enter to skip)', null)
        ->expectsOutput('Message cannot be empty!')
        ->assertExitCode(1);
});

test('it accepts command options', function () {
    $this->markTestSkipped('Skipping Telegram API test');
    $this->telegramServiceMock->shouldReceive('send')
        ->once()
        ->with("*Option Title*\n\nThis is a test message")
        ->andReturnUsing(function () { $this->called = true; return null; });
    $this->artisan('telegram:send', ['--title' => 'Option Title'])
        ->expectsQuestion('Enter your message', 'This is a test message')
        ->expectsQuestion('Enter an image URL (optional, press enter to skip)', null)
        ->expectsOutput('Sending message to Telegram...')
        ->expectsOutput('Message sent successfully!')
        ->assertExitCode(0);
    expect($this->called)->toBeTrue();
});
