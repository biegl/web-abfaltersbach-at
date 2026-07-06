<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// ponytail: api routes only get StartSession via Sanctum's stateful-frontend check,
// which requires a Referer/Origin header. postJson sends none, so
// $request->session() throws "Session store not set" in LoginController::logout.
// Push the middleware directly so the logout flow under test has a session.
beforeEach(function () {
    app('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\Session\Middleware\StartSession');
});

it('logs a user out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('api/logout')
        ->assertStatus(200)
        ->assertJson(['data' => 'User logged out.']);

    expect(Auth::check())->toBeFalse();
});
