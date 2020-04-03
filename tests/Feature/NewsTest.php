<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;
use App\News;

class NewsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_news_entry()
    {
        $this->withoutExceptionHandling();

        $attributes = [
            'date' => Carbon::now(),
            'expirationDate' => Carbon::now()->addDay(),
            'title' => $this->faker->sentence,
            'text' => $this->faker->paragraph,
        ];

        $this->post('/news', $attributes)->assertCreated();

        $this->assertDatabaseHas('tbl_news', $attributes);
    }

    /** @test */
    public function a_user_can_update_a_news_entry()
    {
        $this->withoutExceptionHandling();

        $news = factory('App\News')->create(['ID' => 1]);
        $news->title = 'Test Title';

        $this->put('/news/' . $news->id, $news->toArray())->assertOk();

        $this->assertDatabaseHas('tbl_news', ['ID' => $news->id, 'title' => 'Test Title']);
    }
}
