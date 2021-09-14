<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvent;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    private $itemsPerPage = 25;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $startDateString = request()->query('startDate');
        $endDateString = request()->query('endDate');

        if ($startDateString && $endDateString) {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDateString)->startofDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDateString)->endOfDay();

            return Event::whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->paginate($this->itemsPerPage);
        }

        return Event::orderBy('date', 'desc')
            ->paginate($this->itemsPerPage);
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
        // Delete attachments
        foreach ($event->attachments as $file) {
            Storage::delete([$file->file]);
            $file->delete();
        }

        // Delete event
        $event->delete();

        // Clear cache
        Cache::forget(Event::$CACHE_KEY_CURRENT_EVENTS);
        Cache::forget(Event::$CACHE_KEY_GROUPED_EVENTS);

        return response()->json(null, 204);
    }

    /**
     * Attaches a file to a specific event.
     *
     * @param  \App\Models\Event  $event
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attachFile(Event $event, Request $request)
    {
        $file = FilesController::storeFile($request);
        $event->attachments()->save($file);
        Cache::forget(Event::$CACHE_KEY_CURRENT_EVENTS);
        Cache::forget(Event::$CACHE_KEY_GROUPED_EVENTS);

        return response()->json($event->fresh(), 200);
    }
}
