<?php

use App\Models\News;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('news can be created', function () {
    $payload = [
        'title' => 'Lorem',
        'text' => 'Ipsum',
        'date' => '2020-08-04',
    ];

    $this->actingAs($this->user)
        ->postJson('/api/news', $payload)
        ->assertStatus(201)
        ->assertJson(['title' => 'Lorem', 'text' => 'Ipsum']);
});

test('news can be updated', function () {
    // Arrange
    $news = News::factory()->create([
        'title' => 'First News',
        'text' => 'First Body',
        'date' => '2020-08-04',
    ]);

    $payload = [
        'title' => 'Lorem',
        'text' => 'Ipsum',
        'date' => '2020-08-04',
    ];

    // Act & Assert
    $this->actingAs($this->user)
        ->putJson('/api/news/'.$news->ID, $payload)
        ->assertStatus(200)
        ->assertJson([
            'ID' => $news->ID,
            'title' => 'Lorem',
            'text' => 'Ipsum',
        ]);
});

test('news can be deleted', function () {
    // Arrange
    $news = News::factory()->create([
        'title' => 'First News',
        'text' => 'First Body',
        'date' => '2020-08-04',
    ]);

    // Act & Assert
    $this->actingAs($this->user)
        ->deleteJson('/api/news/'.$news->ID)
        ->assertStatus(204);
});

test('news can be listed', function () {
    // Arrange
    News::factory()->create([
        'title' => 'First News',
        'text' => 'First Body',
    ]);

    News::factory()->create([
        'title' => 'Second News',
        'text' => 'Second Body',
    ]);

    // Act & Assert
    $this->actingAs($this->user)
        ->getJson('/api/news')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['ID', 'title', 'text', 'date', 'expirationDate'],
            ],
        ]);
});
