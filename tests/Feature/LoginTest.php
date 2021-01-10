<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    public function testRequiresEmailAndPassword()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['validation.required'],
                    'password' => ['validation.required']
                ]
            ]);
    }


    public function testUserLogsInSuccessfully()
    {
        $user = User::factory()->create([
            'email' => 'testlogin@user.com',
            'password' => bcrypt('toptal123'),
        ]);

        $payload = ['email' => 'testlogin@user.com', 'password' => 'toptal123'];

        $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'api_token',
                ],
            ]);
    }
}
