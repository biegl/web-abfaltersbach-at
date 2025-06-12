<?php

use App\Console\Commands\SendTelegramMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Telegram\Bot\Api;

uses(RefreshDatabase::class);

test('it sends text message to telegram', function () {
    // Create a mock for the Telegram API
    $telegramMock = Mockery::mock(Api::class);

    // Set expectations
    $telegramMock->shouldReceive('sendMessage')
        ->once()
        ->with(Mockery::on(function ($arg) {
            return $arg['text'] === "*Test Title*\n\nThis is a test message" &&
                   $arg['parse_mode'] === 'Markdown';
        }))
        ->andReturn(true);

    // Create the command with our mock
    $command = new SendTelegramMessage($telegramMock);

    // Run the command
    $this->app->instance(SendTelegramMessage::class, $command);

    $this->artisan('telegram:send')
        ->expectsQuestion('Enter a title for the message', 'Test Title')
        ->expectsQuestion('Enter your message', 'This is a test message')
        ->expectsQuestion('Enter an image URL (optional, press enter to skip)', null)
        ->expectsOutput('Sending message to Telegram...')
        ->expectsOutput('Message sent successfully!')
        ->assertExitCode(0);
});

test('it sends photo message to telegram', function () {
    // Create a mock for the Telegram API
    $telegramMock = Mockery::mock(Api::class);

    // Set expectations
    $telegramMock->shouldReceive('sendPhoto')
        ->once()
        ->with(Mockery::on(function ($arg) {
            return $arg['caption'] === "*Test Title*\n\nThis is a test message with image" &&
                   $arg['photo'] === 'https://example.com/image.jpg' &&
                   $arg['parse_mode'] === 'Markdown';
        }))
        ->andReturn(true);

    // Create the command with our mock
    $command = new SendTelegramMessage($telegramMock);

    // Run the command
    $this->app->instance(SendTelegramMessage::class, $command);

    $this->artisan('telegram:send')
        ->expectsQuestion('Enter a title for the message', 'Test Title')
        ->expectsQuestion('Enter your message', 'This is a test message with image')
        ->expectsQuestion('Enter an image URL (optional, press enter to skip)', 'https://example.com/image.jpg')
        ->expectsOutput('Sending message to Telegram...')
        ->expectsOutput('Message sent successfully!')
        ->assertExitCode(0);
});

test('it handles empty message', function () {
    // Create a mock for the Telegram API
    $telegramMock = Mockery::mock(Api::class);

    // Create the command with our mock
    $command = new SendTelegramMessage($telegramMock);

    // Run the command
    $this->app->instance(SendTelegramMessage::class, $command);

    $this->artisan('telegram:send')
        ->expectsQuestion('Enter a title for the message', 'Test Title')
        ->expectsQuestion('Enter your message', '')
        ->expectsOutput('Message cannot be empty!')
        ->assertExitCode(1);
});

test('it accepts command options', function () {
    // Create a mock for the Telegram API
    $telegramMock = Mockery::mock(Api::class);

    // Set expectations
    $telegramMock->shouldReceive('sendMessage')
        ->once()
        ->with(Mockery::on(function ($arg) {
            return $arg['text'] === "*Option Title*\n\nThis is a test message" &&
                   $arg['parse_mode'] === 'Markdown';
        }))
        ->andReturn(true);

    // Create the command with our mock
    $command = new SendTelegramMessage($telegramMock);

    // Run the command
    $this->app->instance(SendTelegramMessage::class, $command);

    $this->artisan('telegram:send', ['--title' => 'Option Title'])
        ->expectsQuestion('Enter your message', 'This is a test message')
        ->expectsQuestion('Enter an image URL (optional, press enter to skip)', null)
        ->expectsOutput('Sending message to Telegram...')
        ->expectsOutput('Message sent successfully!')
        ->assertExitCode(0);
});
