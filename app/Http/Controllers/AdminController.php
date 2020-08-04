<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function show(Request $request)
    {
        return view('page.admin');
    }
}
