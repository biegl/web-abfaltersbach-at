<?php

namespace App\Http\Controllers;

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

        $key = (new $model)->getKeyName();

        $items = $model::whereIn($key, $listItemIds)->get();

        return $items->sortBy(function ($item) use ($listItemIds, $key) {
            return array_search($item->{$key}, $listItemIds);
        })->values();
    }
}
