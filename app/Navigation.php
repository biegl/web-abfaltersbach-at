<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use App\Router\Helper as RouterHelper;
use Str;

class Navigation extends Model
{
    protected $table = 'tbl_navigation';

    protected $primaryKey = 'ID';

    protected $with = ['children'];

    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    public function getHasParentAttribute()
    {
        return !is_null($this->parent());
    }

    public function getIsActiveAttribute()
    {
        $path = RouterHelper::normalizeUrl(Request::path());
        return Str::contains($path, $this->url);
    }

    public function getSlugAttribute()
    {
        return trim($this->linkname);
    }

    public function getUrlAttribute()
    {
        if (!$this->hasParent) {
            return RouterHelper::normalizeUrl($this->slug);
        }

        return RouterHelper::normalizeUrl($this->parent()->slug . "/" . $this->slug);
    }

    public function children()
    {
        return $this->hasMany('App\Navigation', 'refID');
    }

    public function parent()
    {
        return $this->belongsTo('App\Navigation', 'refID')->first();
    }

    public static function breadcrumbs(\App\Page $page)
    {
        $nav_item = Navigation::where('ID', $page->navigation_id)->first();

        return array_filter(array_unique([
            Navigation::landingPage(),
            $nav_item->parent(),
            $nav_item,
        ]));
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
}
