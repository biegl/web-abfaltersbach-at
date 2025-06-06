<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password confirmation screen can be rendered', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/password/confirm')->assertOk();
});

test('password can be confirmed', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);

    $this->actingAs($user)->post('/password/confirm', [
        'password' => 'password',
    ])->assertRedirect()->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);

    $this->actingAs($user)->post('/password/confirm', [
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('password');
});
