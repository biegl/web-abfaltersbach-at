<?php

namespace App\Router;

use App\Models\Navigation;
use Cache;
use App\Models\Page;
use App\Support\Str;
use App\Router\Helper as RouterHelper;

/**
 * A router for static pages.
 *
 * @package rainlab\pages
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
     * @param string $url The requested URL string.
     * @return \App\Models\Page Returns \App\Models\Page object or null if the page cannot be found.
     */
    public function findByUrl($url)
    {
        $url = Str::lower(RouterHelper::normalizeUrl($url));

        if (array_key_exists($url, self::$cache)) {
            return self::$cache[$url];
        }

        $urlMap = $this->getUrlMap();

        if (!array_key_exists($url, $urlMap)) {
            return null;
        }

        $pageId = $urlMap[$url];

        if (($page = Page::find($pageId)) === null) {

            // If the page was not found, clear the URL cache and try again.
            $this->clearCache();

            return self::$cache[$url] = Page::find($pageId);
        }

        return self::$cache[$url] = $page;
    }

    /**
     * Autoloads the URL map only allowing a single execution.
     * @return array Returns the URL map.
     */
    protected function getUrlMap()
    {
        if (!count(self::$urlMap)) {
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
            return Navigation::getUrlMap();
        });
    }

    /**
     * Clears the router cache.
     */
    public function clearCache()
    {
        Cache::forget(self::$CACHE_KEY_URL_MAP);
    }
}
