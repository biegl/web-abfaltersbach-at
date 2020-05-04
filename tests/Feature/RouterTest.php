<?php

namespace Tests\Feature;

use App\Navigation;
use App\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Cache;

class RouterTest extends TestCase
{
    use RefreshDatabase;

    private $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new Router;
    }

    protected function tearDown(): void
    {
        $this->router->clearCache();
        parent::tearDown();
    }

    /** @test */
    public function it_should_find_a_page_by_url()
    {
        $pageOne = factory(\App\Page::class)->create();
        $pageTwo = factory(\App\Page::class)->create();

        $navOne = factory(\App\Navigation::class)->create(['refID' => $pageOne->id]);
        $navTwo = factory(\App\Navigation::class)->create(['refID' => $pageTwo->id]);

        $page = $this->router->findByUrl($navOne->slug);
        $this->assertNotNull($page);
        $this->assertEquals($pageOne->content, $page->content);
    }

    /** @test */
    public function it_should_not_find_a_non_existing_page()
    {
        $pageOne = factory(\App\Page::class)->create();
        $pageTwo = factory(\App\Page::class)->create();

        $navOne = factory(\App\Navigation::class)->create(['refID' => $pageOne->id, 'linkname' => 'nav-one']);
        $navTwo = factory(\App\Navigation::class)->create(['refID' => $pageTwo->id, 'linkname' => 'nav-two']);

        $page = $this->router->findByUrl('foo-bar');
        $this->assertNull($page);
    }
}
