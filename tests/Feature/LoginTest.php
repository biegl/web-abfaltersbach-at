<?php

use App\Models\User;

// ponytail: api routes only get StartSession via Sanctum's stateful-frontend check,
// which requires a Referer/Origin header. postJson sends none, so
// $request->session() throws "Session store not set" in LoginController.
// Push the middleware directly so the login flow under test has a session.
beforeEach(function () {
    app('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\Session\Middleware\StartSession');
});

it('rejects login without email and password', function () {
    $this->postJson('api/login')
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                'email' => ['validation.required'],
                'password' => ['validation.required'],
            ],
        ]);
});

it('rejects login with wrong credentials', function () {
    User::factory()->create(['email' => 'a@b.c']);

    $this->postJson('api/login', ['email' => 'a@b.c', 'password' => 'wrong'])
        ->assertStatus(401)
        ->assertJson(['errors' => ['email' => ['auth.failed']]]);
});

it('logs a user in successfully', function () {
    $user = User::factory()->create();

    $this->postJson('api/login', ['email' => $user->email, 'password' => 'password'])
        ->assertStatus(200)
        ->assertJsonStructure(['id', 'name', 'email', 'created_at', 'updated_at']);
});
