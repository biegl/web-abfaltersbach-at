<?php

namespace App\Http\Controllers;

use App\News;
use App\Event;
use App\Navigation;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $navigation = Navigation::topLevel()->get();
        $news = News::notExpired()->orderBy('date', 'desc')->limit(20)->get();
        $grouped_events = Event::upcoming()->get()->groupBy(function ($d) {
            return Carbon::parse($d->date)->formatLocalized('%B');
        });

        return view('home.index', compact('navigation', 'news', 'grouped_events'));
    }
}
