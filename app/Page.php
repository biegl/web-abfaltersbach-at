<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\News;
use App\Event;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    protected $table = 'tbl_site';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public $with = ['attachments'];

    protected $fillable = [
        'seitentitel',
        'inhalt',
    ];

    public function getTitleAttribute()
    {
        return $this->seitentitel;
    }

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
        $title = $this->title;
        $content = $this->content;
        $navigation = Navigation::topLevel()->get();
        $breadcrumbs = Navigation::breadcrumbs($this);
        $subnavigation = Navigation::subnavigation($this);

        switch ($this->templateName) {
            case 'page.home':
                $news = Cache::remember(News::$CACHE_KEY_TOP_NEWS, config('cache.defaultTTL'), function () {
                    return News::top()->get();
                });

                // Load grouped events from cache
                $grouped_events = Cache::remember(Event::$CACHE_KEY_GROUPED_EVENTS, config('cache.defaultTTL'), function () {
                    return Event::byMonth();
                });

                $current_events = Cache::remember(Event::$CACHE_KEY_CURRENT_EVENTS, config('cache.defaultTTL'), function () {
                    return Event::current()->get();
                });

                return compact('title', 'content', 'navigation', 'breadcrumbs', 'news', 'grouped_events', 'current_events');
            default:
                $files = $this->files->merge($this->attachments);
                return compact('title', 'content', 'navigation', 'breadcrumbs', 'subnavigation', 'files');
        }
    }

    public function files()
    {
        return $this->hasManyThrough(
            'App\File',
            'App\Navigation',
            'ID', // Foreign key on navigation table
            'navID', // Foreign key on files table
            'navigation_id', // Local key on pages table
            'ID' // Local key on navigation table
        );
    }

    /**
     * Get all of the page's attachments.
     */
    public function attachments()
    {
        return $this->morphMany(File::class, 'attachable');
    }
}
