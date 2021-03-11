<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Navigation;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_create_a_table()
    {
        File::factory()->create(['title' => 'Testfile']);
        $this->assertDatabaseHas('tbl_downloads', ['title' => 'Testfile']);
    }

    /** @test */
    public function it_should_list_all_attached_files_for_a_specific_page()
    {
        // GIVEN
        $page = Page::factory()->create(['navigation_id' => 1]);
        $navItem = Navigation::factory()->create(['ID' => $page->ID]);

        $files = File::factory(5)->create([
            'navID' => $navItem->ID,
        ]);

        $otherFile = File::factory()->create([
            'navID' => $navItem->ID + 1,
        ]);

        // THEN
        $filesInDatabase = File::all();
        $this->assertCount(6, $filesInDatabase);
        $this->assertCount(5, $page->files);
    }
}
