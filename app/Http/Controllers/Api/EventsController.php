<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Event::orderBy('date', 'desc')->limit(20)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEvent  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEvent $request)
    {
        $event = Event::create($request->validated());
        Cache::forget(Event::$CACHE_KEY_CURRENT_EVENTS);
        Cache::forget(Event::$CACHE_KEY_GROUPED_EVENTS);
        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return $event;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreEvent $request, Event $event)
    {
        $event->update($request->validated());
        Cache::forget(Event::$CACHE_KEY_CURRENT_EVENTS);
        Cache::forget(Event::$CACHE_KEY_GROUPED_EVENTS);
        return response()->json($event, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();
        Cache::forget(Event::$CACHE_KEY_CURRENT_EVENTS);
        Cache::forget(Event::$CACHE_KEY_GROUPED_EVENTS);
        return response()->json(null, 204);
    }

    public function attachFile(Event $event, Request $request)
    {
        $file = FilesController::storeFile($request);
        $event->attachments()->save($file);
        Cache::forget(Event::$CACHE_KEY_CURRENT_EVENTS);
        Cache::forget(Event::$CACHE_KEY_GROUPED_EVENTS);
        return response()->json($event->fresh(), 200);
    }
}
