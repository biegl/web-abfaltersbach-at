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

        $ids_ordered = implode(',', $listItemIds);

        $cache_key = self::$CACHE_KEY_LIST.'_'.$id;

        return Cache::remember($cache_key, config('cache.defaultTTL'), function () use ($model, $listItemIds, $ids_ordered) {
            return $model::whereIn('id', $listItemIds)
                ->orderByRaw("FIELD(id, $ids_ordered)")
                ->get();
        });
    }
}
