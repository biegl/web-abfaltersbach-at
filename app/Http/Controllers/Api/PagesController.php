<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePage;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Page::orderBy('seitentitel', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request\StorePage  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePage $request)
    {
        $page = Page::create($request->validated(null, null));

        return response()->json($page, 201);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        return $page;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request\StorePage  $request
     * @return \Illuminate\Http\Response
     */
    public function update(StorePage $request, Page $page)
    {
        $page->update($request->validated(null, null));

        return response()->json($page, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        // Delete attachments
        foreach ($page->attachments as $file) {
            Storage::delete([$file->file]);
            $file->delete();
        }

        // Delete page
        $page->delete();

        return response()->json(null, 204);
    }

    /**
     * Attaches a file to a specific page.
     *
     * @return \Illuminate\Http\Response
     */
    public function attachFile(Page $page, Request $request)
    {
        $file = FilesController::storeFile($request);
        $page->attachments()->save($file);

        return response()->json($page->fresh(), 200);
    }
}
