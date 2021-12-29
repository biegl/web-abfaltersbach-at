<?php

namespace App\Http\Filters;

class NewsFilter extends QueryFilter
{
    public function newsID($id = null)
    {
        if (is_null($id)) {
            return $this->builder;
        }

        return $this->builder->where('ID', $id);
    }
}
