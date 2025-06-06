<?php

namespace Tests\Feature;

use App\Models\Navigation;
use App\Models\Page;
use App\Router\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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

    #[Test]
    public function it_should_find_a_page_by_url()
    {
        $pageOne = Page::factory()->create();
        $pageTwo = Page::factory()->create();

        $navOne = Navigation::factory()->create(['refID' => $pageOne->id]);
        $navTwo = Navigation::factory()->create(['refID' => $pageTwo->id]);

        $page = $this->router->findByUrl($navOne->slug);
        $this->assertNotNull($page);
        $this->assertEquals($pageOne->content, $page->content);
    }

    #[Test]
    public function it_should_not_find_a_non_existing_page()
    {
        $pageOne = Page::factory()->create();
        $pageTwo = Page::factory()->create();

        $navOne = Navigation::factory()->create(['refID' => $pageOne->id, 'linkname' => 'nav-one']);
        $navTwo = Navigation::factory()->create(['refID' => $pageTwo->id, 'linkname' => 'nav-two']);

        $page = $this->router->findByUrl('foo-bar');
        $this->assertNull($page);
    }
}
