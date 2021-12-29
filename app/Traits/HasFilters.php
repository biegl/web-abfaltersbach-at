<?php

namespace App\Traits;

use App\Http\Filters\QueryFilter;

trait HasFilters
{
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
