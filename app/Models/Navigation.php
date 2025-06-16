<?php

namespace App\Models;

use App\Router\Helper as RouterHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function hasChildren(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->children()->count() > 0;
        });
    }

    protected function hasParent(): Attribute
    {
        return Attribute::make(get: function () {
            return ! is_null($this->parent());
        });
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(get: function () {
            $path = RouterHelper::normalizeUrl(Request::path());
            return Str::contains($path, $this->url);
        });
    }

    protected function slug(): Attribute
    {
        return Attribute::make(get: function () {
            return trim($this->linkname);
        });
    }

    protected function url(): Attribute
    {
        return Attribute::make(get: function () {
            if (! $this->hasParent) {
                return RouterHelper::normalizeUrl('/'.$this->slug);
            }
            return RouterHelper::normalizeUrl('/'.$this->parent()->slug.'/'.$this->slug);
        });
    }

    protected function pageId(): Attribute
    {
        return Attribute::make(get: function () {
            $page = \App\Models\Page::where('navigation_id', $this->ID)->first();
            if ($page) {
                return \App\Models\Page::where('navigation_id', $this->ID)->first()->ID;
            }
            return null;
        });
    }

    protected function isVisible(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->navianzeigen == 'Ja';
        });
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

            // Get the page ID from the relationship
            $pageId = Page::where('navigation_id', $page->ID)->first()?->ID;
            if ($pageId) {
                // Correct URL for startpage
                if ($url === '/startseite') {
                    $url = '/';
                }

                $map[$url] = $pageId;
            }
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
