<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

test('user can logout', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/logout');

    // Assert
    expect(Auth::check())->toBeFalse();
});
