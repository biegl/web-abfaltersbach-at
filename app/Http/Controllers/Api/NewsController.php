<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNews;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return News::orderBy('date', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNews  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNews $request)
    {
        $news = News::create($request->validated());
        Cache::forget(News::$CACHE_KEY_TOP_NEWS);

        return response()->json($news, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        return $news;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(StoreNews $request, News $news)
    {
        $news->update($request->validated());
        Cache::forget(News::$CACHE_KEY_TOP_NEWS);

        return response()->json($news, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        // Delete attachments
        foreach ($news->attachments as $file) {
            Storage::delete([$file->file]);
            $file->delete();
        }

        // Delete news
        $news->delete();

        // Clear cache
        Cache::forget(News::$CACHE_KEY_TOP_NEWS);

        return response()->json(null, 204);
    }

    /**
     * Attaches a file to a specific news.
     * @param  \App\Models\News $news
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function attachFile(News $news, Request $request)
    {
        $file = FilesController::storeFile($request);
        $news->attachments()->save($file);
        Cache::forget(News::$CACHE_KEY_TOP_NEWS);

        return response()->json($news->fresh(), 200);
    }
}
