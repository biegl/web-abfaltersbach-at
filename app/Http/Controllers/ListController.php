<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class ListController extends Controller
{
    public static $CACHE_KEY_LIST = 'CACHE_KEY_LIST';

    public static function getItems(int $id, array $config)
    {
        $model = $config['model'];
        $listItemIds = $config['ids'];

        if (empty($listItemIds)) {
            return [];
        }

        $items = $model::whereIn('id', $listItemIds)->get();
        return $items->sortBy(function ($item) use ($listItemIds) {
            return array_search($item->id, $listItemIds);
        })->values();
    }
}
