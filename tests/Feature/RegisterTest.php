<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_registers_successfully()
    {
        $payload = [
            'name' => 'John',
            'email' => 'john@toptal.com',
            'password' => 'toptal123',
            'password_confirmation' => 'toptal123',
        ];

        $this->postJson('/api/register', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                    'api_token',
                ],
            ]);
    }

    public function test_requires_password_email_and_name()
    {
        $this->postJson('/api/register')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['validation.required'],
                    'email' => ['validation.required'],
                    'password' => ['validation.required'],
                ],
            ]);
    }

    public function test_require_password_confirmation()
    {
        $payload = [
            'name' => 'John',
            'email' => 'john@toptal.com',
            'password' => 'toptal123',
        ];

        $this->postJson('/api/register', $payload)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => ['Die Passwörter stimmen nicht überein'],
                ],
            ]);
    }
}
