<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('verification notice can be rendered', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($user)->get('/email/verify')->assertOk();
});

test('verified user is redirected', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/email/verify')->assertRedirect(config('app.url').'/home');
});

test('email can be verified', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
    );

    $this->actingAs($user)->get($verificationUrl)->assertRedirect(RouteServiceProvider::HOME);
    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->getKey(), 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verification link can be resent', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $this->actingAs($user)->post(route('verification.resend'))
        ->assertRedirect();
});
