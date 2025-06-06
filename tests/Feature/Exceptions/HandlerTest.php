<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('it returns a json 404 response for model not found exception', function () {
    // Create and authenticate as an admin user
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    // Define a temporary, authenticated route that throws the exception
    Route::get('/__test-404', function () {
        throw new ModelNotFoundException();
    })->middleware(['api', 'auth:sanctum']);

    // Make a request to the temporary route
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('/__test-404');

    $response->assertStatus(404)
        ->assertJson(['data' => 'Resource not found']);
});
