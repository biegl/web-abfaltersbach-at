<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        return response(file_get_contents(public_path('admin/index.html')))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
