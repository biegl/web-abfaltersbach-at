<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogoutTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserIsLoggedOutProperly()
    {
        $response = $this->actingAs($this->user)->postJson('/api/logout');
        $this->assertFalse(Auth::check());
    }
}
