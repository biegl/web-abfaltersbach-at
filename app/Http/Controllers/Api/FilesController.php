<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\File;
use App\Http\Requests\StoreFile;
use Illuminate\Http\Request;

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
     * @param  \App\Http\Requests\StoreNews  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFile $request)
    {
        $news = File::create($request->validated());
        return response()->json($news, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  File  $file
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
    public function update(StoreFile $request, File $file)
    {
        $file->update($request->validated());
        return response()->json($file, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        $file->delete();
        return response()->json(null, 204);
    }
}
