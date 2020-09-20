<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\File;

class FilesController extends Controller
{
    public function download(String $name, Request $request)
    {
        // Check if file exists
        $file = File::where(['title' => $name])->firstOrFail();
        return Storage::download($file->file, $file->title);
    }
}
