<?php

namespace App\Http\Controllers;

use App\News;
use App\Event;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $news = News::notExpired()->orderBy('date', 'desc')->limit(20)->get();
        $grouped_events = Event::upcoming()->get()->groupBy(function ($d) {
            return Carbon::parse($d->date)->format('F');
        });

        return view('home.index', compact('news', 'grouped_events'));
    }
}
