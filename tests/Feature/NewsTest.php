<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\News;

class NewsTest extends TestCase
{
    public function testNewsAreCreatedCorrectly()
    {
        $user = User::factory()->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $payload = [
            'title' => 'Lorem',
            'text' => 'Ipsum',
            'date' => '2020-08-04',
        ];

        $this->json('POST', '/api/news', $payload, $headers)
            ->assertStatus(201)
            ->assertJson(['title' => 'Lorem', 'text' => 'Ipsum']);
    }

    public function testNewsAreUpdatedCorrectly()
    {
        $user = User::factory()->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
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

        $response = $this->json('PUT', '/api/news/' . $news->ID, $payload, $headers)
            ->assertStatus(200)
            ->assertJson([
                'ID' => $news->ID,
                'title' => 'Lorem',
                'text' => 'Ipsum',
            ]);
    }

    public function testNewsAreDeletedCorrectly()
    {
        $user = User::factory()->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $news = News::factory()->create([
            'title' => 'First News',
            'text' => 'First Body',
            'date' => '2020-08-04',
        ]);

        $this->json('DELETE', '/api/news/' . $news->ID, [], $headers)
            ->assertStatus(204);
    }

    public function testNewsAreListedCorrectly()
    {
        News::factory()->create([
            'title' => 'First News',
            'text' => 'First Body'
        ]);

        News::factory()->create([
            'title' => 'Second News',
            'text' => 'Second Body'
        ]);

        $user = User::factory()->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', '/api/news', [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['ID', 'title', 'text', 'date', 'expirationDate'],
            ]);
    }
}
