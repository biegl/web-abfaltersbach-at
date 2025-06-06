<?php

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

test('returns a mail message', function () {
    $user = User::factory()->create();
    $notification = new ResetPassword('test-token');
    $message = $notification->toMail($user);

    expect($message)->toBeInstanceOf(MailMessage::class);
});

test('mail message has correct subject', function () {
    $user = User::factory()->create();
    $notification = new ResetPassword('test-token');

    Lang::shouldReceive('get')
        ->with('email.reset.subject')
        ->andReturn('Reset Your Password');

    Lang::shouldReceive('get')->andReturn(''); // Default for others

    $message = $notification->toMail($user);

    expect($message->subject)->toBe('Reset Your Password');
});

test('mail message has correct content', function () {
    $user = User::factory()->create();
    $notification = new ResetPassword('test-token');

    $passwordBroker = config('auth.defaults.passwords');
    $expire = config("auth.passwords.$passwordBroker.expire");
    $expirationLine = "This password reset link will expire in $expire minutes.";

    Lang::shouldReceive('get')
        ->with('email.reset.intro')
        ->andReturn('You are receiving this email because we received a password reset request for your account.');
    Lang::shouldReceive('get')
        ->with('email.reset.expiration', ['count' => $expire])
        ->andReturn($expirationLine);
    Lang::shouldReceive('get')
        ->with('email.reset.noaction')
        ->andReturn('If you did not request a password reset, no further action is required.');

    Lang::shouldReceive('get')->andReturn(''); // Default for others

    $message = $notification->toMail($user);

    expect($message->introLines)->toContain('You are receiving this email because we received a password reset request for your account.');
    expect($message->introLines)->toContain($expirationLine);
    expect($message->introLines)->toContain('If you did not request a password reset, no further action is required.');
});

test('mail message has correct action', function () {
    $user = User::factory()->create();
    $notification = new ResetPassword('test-token');

    Lang::shouldReceive('get')
        ->with('email.reset.action')
        ->andReturn('Reset Password');

    Lang::shouldReceive('get')->andReturn(''); // Default for others

    $message = $notification->toMail($user);

    $url = url(route('password.reset', [
        'token' => 'test-token',
        'email' => $user->getEmailForPasswordReset(),
    ], false));

    expect($message->actionText)->toBe('Reset Password');
    expect($message->actionUrl)->toBe($url);
});
