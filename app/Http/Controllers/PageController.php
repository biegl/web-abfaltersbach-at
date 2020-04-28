<?php

namespace App\Http\Controllers;

use App\News;
use App\Event;
use App\Navigation;
use App\Router;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(Request $request)
    {
        $router = new Router;

        // Find page by URL
        $page = $router->findByUrl($request->getPathInfo());

        if (!$page) {
            $navigation = Navigation::topLevel()->get();
            $breadcrumbs = [];

            return view('errors.404', compact('navigation', 'breadcrumbs'));
        }

        $modules = $page->modules;

        return view($page->templateName, $modules);
    }
}
