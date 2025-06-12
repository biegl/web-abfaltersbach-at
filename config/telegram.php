<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot API Access Token [REQUIRED]
    |--------------------------------------------------------------------------
    |
    | Your Telegram's Bot Access Token.
    | Example: 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
    |
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Username [OPTIONAL]
    |--------------------------------------------------------------------------
    |
    | Your Telegram Bot's Username.
    | Example: BotFather
    |
    */
    'bot_username' => env('TELEGRAM_BOT_NAME', 'abfaltersbach_news_bot'),

    /*
    |--------------------------------------------------------------------------
    | Default Channel [OPTIONAL]
    |--------------------------------------------------------------------------
    |
    | Your Telegram Bot's Default Channel.
    | Example: @abfaltersbach
    |
    */
    'default_channel' => env('TELEGRAM_CHANNEL_NAME', '@abfaltersbach'),

    /*
    |--------------------------------------------------------------------------
    | Asynchronous Requests [Optional]
    |--------------------------------------------------------------------------
    |
    | When set to True, All the requests would be made non-blocking (Async)
    |
    */
    'async_requests' => env('TELEGRAM_ASYNC_REQUESTS', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Handler [Optional]
    |--------------------------------------------------------------------------
    |
    | If you'd like to use a custom HTTP Client Handler.
    | Should be an instance of \Telegram\Bot\HttpClients\HttpClientInterface
    |
    */
    'http_client_handler' => null,

    /*
    |--------------------------------------------------------------------------
    | Register Telegram Commands [Optional]
    |--------------------------------------------------------------------------
    |
    | If you'd like to use the SDK's built in command handler system,
    | You can register all the commands here.
    |
    | The command class should extend the \Telegram\Bot\Commands\Command class.
    |
    | Default: The SDK registers, a help command which when a user sends /help
    | will respond with a list of available commands and description.
    |
    */
    'commands' => [
        // Telegram\Bot\Commands\HelpCommand::class,
    ],
]; 