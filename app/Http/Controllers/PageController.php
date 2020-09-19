<?php

namespace App\Http\Controllers;

use App\Navigation;
use App\Router;
use Illuminate\Http\Request;

class PageController extends Controller
{

    /**
     * Redirects the user to the landing page.
     */
    public function landing(Request $request)
    {
        return redirect('/');
    }

    public function show(Request $request)
    {
        $router = new Router;

        // Find page by URL
        $page = $router->findByUrl($request->getPathInfo());

        if (!$page) {
            $navigation = Navigation::topLevel()->get();
            $breadcrumbs = [];
            $title = "Not Found";

            return view('errors.404', compact('title', 'navigation', 'breadcrumbs'));
        }

        $modules = $page->modules;

        return view($page->templateName, $modules);
    }
}
