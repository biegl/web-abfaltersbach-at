<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListController extends Controller
{
    static function getItems($type, $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $ids_ordered = implode(',', $ids);

        return $type::whereIn('id', $ids)
            ->orderByRaw("FIELD(id, $ids_ordered)")
            ->get();
    }
}
