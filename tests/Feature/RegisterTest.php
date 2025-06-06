<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testRegistersSuccessfully()
    {
        $payload = [
            'name' => 'John',
            'email' => 'john@toptal.com',
            'password' => 'toptal123',
            'password_confirmation' => 'toptal123',
        ];

        $response = $this->post('/api/register', $payload);

        $response->assertStatus(204);
        $this->assertAuthenticated();
    }

    public function testRequiresPasswordEmailAndName()
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

    public function testRequirePasswordConfirmation()
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
