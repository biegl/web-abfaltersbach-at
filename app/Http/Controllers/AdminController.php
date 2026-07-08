<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        return response()->file(public_path('admin/index.html'));
    }
}
