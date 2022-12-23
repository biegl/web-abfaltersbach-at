<?php

namespace App\Models;

use App\Router\Helper as RouterHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Request;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Navigation extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'tbl_navigation';

    protected $primaryKey = 'ID';

    protected $with = ['children'];

    protected $hidden = ['navianzeigen'];

    protected $appends = ['pageId', 'isVisible'];

    public $timestamps = false;

    protected static $logAttributes = ['*'];

    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    public function getHasParentAttribute()
    {
        return ! is_null($this->parent());
    }

    public function getIsActiveAttribute()
    {
        $path = RouterHelper::normalizeUrl(Request::path());

        return Str::contains($path, $this->url);
    }

    public function getSlugAttribute(): string
    {
        return trim($this->linkname);
    }

    public function getUrlAttribute()
    {
        if (! $this->hasParent) {
            return RouterHelper::normalizeUrl($this->slug);
        }

        return RouterHelper::normalizeUrl($this->parent()->slug.'/'.$this->slug);
    }

    public function getPageIdAttribute()
    {
        $page = \App\Models\Page::where('navigation_id', $this->ID)->first();
        if ($page) {
            return \App\Models\Page::where('navigation_id', $this->ID)->first()->ID;
        }

        return null;
    }

    public function getIsVisibleAttribute()
    {
        return $this->navianzeigen == 'Ja';
    }

    public function children()
    {
        return $this->hasMany('App\Models\Navigation', 'refID')->orderBy('position');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Navigation', 'refID')->first();
    }

    public function files()
    {
        return $this->hasMany('App\Models\File', 'navID')->orderBy('position', 'asc');
    }

    public static function breadcrumbs(Page $page)
    {
        $nav_item = self::where('ID', $page->navigation_id)->first();

        return array_filter(array_unique([
            self::landingPage(),
            $nav_item->parent(),
            $nav_item,
        ]));
    }

    public static function subnavigation(Page $page)
    {
        $nav_item = self::where('ID', $page->navigation_id)->first();

        if ($nav_item->parent()) {
            return $nav_item
                ->parent()
                ->children()
                ->visible()
                ->orderBy('position')
                ->get();
        }

        return [];
    }

    public static function getUrlMap()
    {
        $pages = self::visible()->get();

        $map = [];

        foreach ($pages as $page) {
            $url = $page->url;

            if (! $url) {
                continue;
            }

            $url = Str::lower(RouterHelper::normalizeUrl($url));

            // Correct URL for startpage
            if ($url === '/startseite') {
                $url = '/';
            }

            $map[$url] = $page->ID;
        }

        return $map;
    }

    /**
     * Scope a query to only include top level navigation items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTopLevel($query)
    {
        return $query
            ->visible()
            ->where('refID', null)
            ->orderBy('position');
    }

    public function scopeAllTopLevel($query)
    {
        return $query
            ->where('refID', null)
            ->orderBy('position');
    }

    public function scopeLandingPage($query)
    {
        return $query
            ->visible()
            ->where('linkname', 'startseite')
            ->first();
    }

    /**
     * Scope a query to only include visible navigation items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('navianzeigen', 'Ja');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
