<?php

namespace App\Http\Filters;

class EventFilter extends QueryFilter
{
    public function eventID($id = null)
    {
        if (is_null($id)) {
            return $this->builder;
        }

        return $this->builder->where('ID', $id);
    }
}
