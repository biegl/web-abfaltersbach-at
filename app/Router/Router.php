<?php

namespace App\Router;

use App\Models\Navigation;
use App\Models\Page;
use App\Router\Helper as RouterHelper;
use App\Support\Str;
use Cache;
use Illuminate\Support\Facades\Log;

/**
 * A router for static pages.
 *
 * @author Alexey Bobkov, Samuel Georges
 */
class Router
{
    /**
     * @var array Contains the URL map - the list of page IDs and corresponding URL patterns.
     */
    private static $urlMap = [];

    /**
     * @var string The name of the cache bucket.
     */
    private static $CACHE_KEY_URL_MAP = 'navigation.urlmap';

    /**
     * @var array Request-level cache
     */
    private static $cache = [];

    /**
     * Finds a page by its URL.
     *
     * @param  string  $url  The requested URL string.
     * @return \App\Models\Page Returns \App\Models\Page object or null if the page cannot be found.
     */
    public function findByUrl($url)
    {
        $url = Str::lower(RouterHelper::normalizeUrl($url));
        Log::info('Looking for URL: '.$url);

        if (array_key_exists($url, self::$cache)) {
            Log::info('Found in cache');

            return self::$cache[$url];
        }

        $urlMap = $this->getUrlMap();
        Log::info('URL Map: '.json_encode($urlMap));

        if (! array_key_exists($url, $urlMap)) {
            Log::info('URL not found in map');

            return null;
        }

        $pageId = $urlMap[$url];
        Log::info('Found page ID: '.$pageId);

        if (($page = Page::find($pageId)) === null) {
            // If the page was not found, clear the URL cache and try again.
            $this->clearCache();
            Log::info('Page not found in database, cleared cache');

            return self::$cache[$url] = Page::find($pageId);
        }

        return self::$cache[$url] = $page;
    }

    /**
     * Autoloads the URL map only allowing a single execution.
     *
     * @return array Returns the URL map.
     */
    protected function getUrlMap()
    {
        if (self::$urlMap === []) {
            $this->loadUrlMap();
        }

        return self::$urlMap;
    }

    /**
     * Loads the URL map - a list of page URLs and corresponding page IDs.
     * The URL map is cached. The clearCache() method resets the cache.
     */
    protected function loadUrlMap()
    {
        self::$urlMap = Cache::remember(self::$CACHE_KEY_URL_MAP, config('cache.defaultTTL'), function () {
            $map = Navigation::getUrlMap();
            Log::info('Generated URL map: '.json_encode($map));

            return $map;
        });
    }

    /**
     * Clears the URL cache.
     */
    public function clearCache()
    {
        Cache::forget(self::$CACHE_KEY_URL_MAP);
        self::$urlMap = [];
        self::$cache = [];
    }
}
