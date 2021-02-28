<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\News;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;

class Page extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tbl_site';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public $with = ['attachments'];

    protected static $logFillable = true;

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
                $inserts = $this->inserts;
                return compact('title', 'content', 'navigation', 'breadcrumbs', 'subnavigation', 'files', 'inserts');
        }
    }

    public function files()
    {
        return $this->hasManyThrough(
            'App\Models\File',
            'App\Models\Navigation',
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

    /**
     * Get all of the page's modules.
     */
    public function inserts()
    {
        return $this->morphMany(Module::class, 'insertable');
    }
}
