<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\News;
use App\Event;
use Cache;

class Page extends Model
{
    protected $table = 'tbl_site';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID',
        'seitentitel',
        'inhalt',
    ];

    /**
     * @var string The name of the cache bucket.
     */
    private static $CACHE_KEY_GROUPED_EVENTS = 'events.grouped_by_month';

    /**
     * @var string The name of the cache bucket.
     */
    private static $CACHE_KEY_TOP_NEWS = 'news.top';

    public function getContentAttribute()
    {
        return $this->inhalt;
    }

    public function getIsLandingPageAttribute()
    {
        return $this->template === 'template_home.php';
    }

    public function getTemplateNameAttribute()
    {
        if ($this->isLandingPage) {
            return 'page.home';
        }

        return 'page.default';
    }

    public function getModulesAttribute()
    {
        $content = $this->content;
        $navigation = Navigation::topLevel()->get();
        $breadcrumbs = Navigation::breadcrumbs($this);

        switch ($this->templateName) {
            case 'page.home':
                $news = Cache::remember(self::$CACHE_KEY_TOP_NEWS, config('cache:defaultTTL'), function () {
                    return News::top()->get();
                });

                // Load grouped events from cache
                $grouped_events = Cache::remember(self::$CACHE_KEY_GROUPED_EVENTS, config('cache.defaultTTL'), function () {
                    return Event::byMonth();
                });

                return compact('content', 'navigation', 'breadcrumbs', 'news', 'grouped_events');
            default:
                return compact('content', 'navigation', 'breadcrumbs');
        }
    }
}
