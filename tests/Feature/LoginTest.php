<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Session needs to be started before login works.
        $kernel = app('Illuminate\Contracts\Http\Kernel');
        $kernel->pushMiddleware('Illuminate\Session\Middleware\StartSession');
    }

    public function testRequiresEmailAndPassword()
    {
        $this->postJson('api/login')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['validation.required'],
                    'password' => ['validation.required'],
                ],
            ]);
    }

    public function testUserLogsInSuccessfully()
    {
        $this->actingAs($this->user)->postJson('api/login', ['email' => $this->user->email, 'password' => 'password'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
                'api_token',
            ]);
    }
}
