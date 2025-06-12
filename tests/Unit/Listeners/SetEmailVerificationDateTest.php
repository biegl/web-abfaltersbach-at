<?php

use App\Listeners\SetEmailVerificationDate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;

test('sets email verification date on user', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $event = new Verified($user);

    $this->assertNull($user->email_verified_at);

    $listener = new SetEmailVerificationDate;
    $listener->handle($event);

    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('does not change existing verification date', function () {
    $now = Carbon::now();
    $user = User::factory()->create(['email_verified_at' => $now]);
    $event = new Verified($user);

    $listener = new SetEmailVerificationDate;
    $listener->handle($event);

    $this->assertSame(
        $now->toDateTimeString(),
        $user->fresh()->email_verified_at->toDateTimeString()
    );
});
