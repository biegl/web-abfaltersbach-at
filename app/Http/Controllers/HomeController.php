<?php

namespace App\Http\Controllers;

use App\News;

class HomeController extends Controller
{
    public function index()
    {
        $news = News::notExpired()->orderBy('date', 'desc')->limit(20)->get();
        return view('home.index', compact('news'));
    }
}
