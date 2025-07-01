<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFile;
use App\Models\Event;
use App\Models\File;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return File::orderBy('title', 'asc')
            ->limit(20)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFile  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = self::storeFile($request);

        return response()->json($file, 201);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        return $file;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFile $request, File $file)
    {
        $file->update(['title' => $request->title]);

        return response()->json($file->fresh(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        Storage::delete([$file->file]);
        $file->delete();

        // Delete cache if necessary
        if (property_exists($file, 'attachable') && $file->attachable !== null && get_class($file->attachable) == 'App\Models\Event') {
            Cache::forget(Event::$CACHE_KEY_CURRENT_EVENTS);
            Cache::forget(Event::$CACHE_KEY_GROUPED_EVENTS);
        }

        if (property_exists($file, 'attachable') && $file->attachable !== null && get_class($file->attachable) == 'App\Models\News') {
            Cache::forget(News::$CACHE_KEY_TOP_NEWS);
        }

        return response()->json(null, 204);
    }

    public static function storeFile(Request $request)
    {
        if (! $request->hasFile('file')) {
            return null;
        }

        // Collect file information
        $originalName = $request->file->getClientOriginalName();
        $extension = $request->file->getClientOriginalExtension();

        // Create name for storage
        $fileName = Str::random(40).'.'.$extension;

        // Store file
        $filePath = $request->file('file')->storeAs('', $fileName, File::$DISK_NAME);

        // Save information in database
        $fileModel = File::create([
            'title' => $originalName,
            'file' => $filePath,
        ]);

        return $fileModel;
    }
}
