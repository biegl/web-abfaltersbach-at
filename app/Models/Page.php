<?php

namespace App\Models;

use App\Http\Filters\EventFilter;
use App\Http\Filters\NewsFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Page extends Model
{
    use HasFactory;
    use LogsActivity;

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

                // Check if request has query params
                if (request()->has('newsID')) {
                    $news = News::filter(new NewsFilter(request()))->get();
                } else {
                    $news = Cache::remember(News::$CACHE_KEY_TOP_NEWS, config('cache.defaultTTL'), function () {
                        return News::top()->get();
                    });
                }

                // Check if request has query params
                if (request()->has('eventID')) {
                    $current_events = Event::filter(new EventFilter(request()))->get();
                    $grouped_events = [];
                } else {
                    // Load grouped events from cache
                    $grouped_events = Cache::remember(Event::$CACHE_KEY_GROUPED_EVENTS, config('cache.defaultTTL'), function () {
                        return Event::byMonth();
                    });

                    $current_events = Cache::remember(Event::$CACHE_KEY_CURRENT_EVENTS, config('cache.defaultTTL'), function () {
                        return Event::current()->get();
                    });
                }

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
        )->orderBy('created_at', 'desc');
    }

    /**
     * Get all of the page's attachments.
     */
    public function attachments()
    {
        return $this->morphMany(File::class, 'attachable')->orderBy('created_at', 'desc');
    }

    /**
     * Get all of the page's modules.
     */
    public function inserts()
    {
        return $this->morphMany(Module::class, 'insertable');
    }

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults();
    }
}
