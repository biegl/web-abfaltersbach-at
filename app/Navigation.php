<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;

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
        return Request::is($this->url);
    }

    public function getSlugAttribute()
    {
        return $this->linkname;
    }

    public function getUrlAttribute()
    {
        if (!$this->hasParent) {
            return "/" . $this->slug;
        }

        return "/" . $this->parent()->slug . "/" . $this->slug;
    }

    public function children()
    {
        return $this->hasMany('App\Navigation', 'refID');
    }

    public function parent()
    {
        return $this->belongsTo('App\Navigation', 'refID')->first();
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
