<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function download(string $name, Request $request)
    {
        // Check if file exists
        $file = File::where(['title' => $name])
            ->orWhere(['file' => $name])
            ->firstOrFail();

        return Storage::download($file->file, $file->title);
    }
}
