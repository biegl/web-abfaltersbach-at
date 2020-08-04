<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\News;

class NewsTest extends TestCase
{
    public function testsNewsAreCreatedCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $payload = [
            'title' => 'Lorem',
            'body' => 'Ipsum',
        ];

        $this->json('POST', '/api/articles', $payload, $headers)
            ->assertStatus(200)
            ->assertJson(['id' => 1, 'title' => 'Lorem', 'body' => 'Ipsum']);
    }

    public function testsNewsAreUpdatedCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $article = factory(News::class)->create([
            'title' => 'First Article',
            'text' => 'First Body',
        ]);

        $payload = [
            'title' => 'Lorem',
            'text' => 'Ipsum',
        ];

        $response = $this->json('PUT', '/api/news/' . $article->id, $payload, $headers)
            ->assertStatus(200)
            ->assertJson([
                'id' => 1,
                'title' => 'Lorem',
                'text' => 'Ipsum'
            ]);
    }

    public function testsNewsAreDeletedCorrectly()
    {
        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];
        $news = factory(News::class)->create([
            'title' => 'First News',
            'text' => 'First Body',
        ]);

        $this->json('DELETE', '/api/news/' . $news->id, [], $headers)
            ->assertStatus(204);
    }

    public function testNewsAreListedCorrectly()
    {
        factory(News::class)->create([
            'title' => 'First News',
            'text' => 'First Body'
        ]);

        factory(News::class)->create([
            'title' => 'Second News',
            'text' => 'Second Body'
        ]);

        $user = factory(User::class)->create();
        $token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', '/api/news', [], $headers)
            ->assertStatus(200)
            ->assertJson([
                [
                    'ID' => 1,
                    'date' => '2020-08-04T06 =>01 =>32.000000Z',
                    'expirationDate' => '2020-10-02T06:01:32.000000Z',
                    'title' => 'First News',
                    'text' => 'First Body',
                    'galleryId' => null
                ],
                [
                    'ID' => 2,
                    'date' => '2020-08-04T06 =>01 =>32.000000Z',
                    'expirationDate' => '2021-03-28T06 =>01 =>32.000000Z',
                    'title' => 'Second News',
                    'text' => 'Second Body',
                    'galleryId' => null
                ]
            ])
            ->assertJsonStructure([
                '*' => ['ID', 'body', 'text'],
            ]);
    }
}
