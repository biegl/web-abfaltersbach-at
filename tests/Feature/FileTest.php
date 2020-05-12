<?php

namespace Tests\Feature;

use App\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_create_a_table()
    {
        factory(\App\File::class)->create(['title' => 'Testfile']);
        $this->assertDatabaseHas('tbl_downloads', ['title' => 'Testfile']);
    }

    /** @test */
    public function it_should_list_all_attached_files_for_a_specific_page()
    {
        // GIVEN
        $page = factory(\App\Page::class)->create(['navigation_id' => 1]);
        $navItem = factory(\App\Navigation::class)->create([ 'ID' => $page->ID ]);

        $files = factory(\App\File::class, 5)->create([
            'navID' => $navItem->ID,
        ]);

        $otherFile = factory(\App\File::class, 1)->create([
            'navID' => $navItem->ID + 1,
        ]);

        // THEN
        $filesInDatabase = File::all();
        $this->assertCount(6, $filesInDatabase);
        $this->assertCount(5, $page->files);
    }
}
