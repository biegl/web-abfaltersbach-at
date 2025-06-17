<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_is_logged_out_properly()
    {
        $response = $this->actingAs($this->user)->postJson('/api/logout');
        $this->assertFalse(Auth::check());
    }
}
