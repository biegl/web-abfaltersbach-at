<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Tests\TestCase;

class NewsTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testNewsAreCreatedCorrectly()
    {
        $payload = [
            'title' => 'Lorem',
            'text' => 'Ipsum',
            'date' => '2020-08-04',
        ];

        $this->actingAs($this->user)->postJson('/api/news', $payload)
            ->assertStatus(201)
            ->assertJson(['title' => 'Lorem', 'text' => 'Ipsum']);
    }

    public function testNewsAreUpdatedCorrectly()
    {
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

        $this->actingAs($this->user)->putJson('/api/news/'.$news->ID, $payload)
            ->assertStatus(200)
            ->assertJson([
                'ID' => $news->ID,
                'title' => 'Lorem',
                'text' => 'Ipsum',
            ]);
    }

    public function testNewsAreDeletedCorrectly()
    {
        $news = News::factory()->create([
            'title' => 'First News',
            'text' => 'First Body',
            'date' => '2020-08-04',
        ]);

        $this->actingAs($this->user)->deleteJson('/api/news/'.$news->ID, [])
            ->assertStatus(204);
    }

    public function testNewsAreListedCorrectly()
    {
        News::factory()->create([
            'title' => 'First News',
            'text' => 'First Body',
        ]);

        News::factory()->create([
            'title' => 'Second News',
            'text' => 'Second Body',
        ]);

        $this->actingAs($this->user)->getJson('/api/news', [])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['ID', 'title', 'text', 'date', 'expirationDate'],
                ],
            ]);
    }
}
