<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ListController;
use App\Http\Requests\StorePerson;
use App\Http\Requests\StorePersonListRequest;
use App\Models\File;
use App\Models\Module;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PersonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Person::orderBy('name', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePerson  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePerson $request)
    {
        $person = Person::create($request->validated());

        Cache::forget(ListController::$CACHE_KEY_LIST.'_1');
        Cache::forget(ListController::$CACHE_KEY_LIST.'_2');

        return response()->json($person, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        return $person;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(StorePerson $request, Person $person)
    {
        $person->update($request->validated());

        Cache::forget(ListController::$CACHE_KEY_LIST.'_1');
        Cache::forget(ListController::$CACHE_KEY_LIST.'_2');

        return response()->json($person, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        $image = $person->image()->get()->first();

        // Delete attachments
        if (isset($image)) {
            Storage::delete([$image->file]);
            $person->image()->delete();
        }

        // Delete person
        $person->delete();

        Cache::forget(ListController::$CACHE_KEY_LIST.'_1');
        Cache::forget(ListController::$CACHE_KEY_LIST.'_2');

        return response()->json(null, 204);
    }

    /**
     * Attaches a file to a specific event.
     * @param  \App\Models\Person $person
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function attachFile(Person $person, Request $request)
    {
        $file = FilesController::storeFile($request);
        $person->image()->save($file);

        return response()->json($person->fresh(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function deleteFile(Person $person, File $file)
    {
        Storage::delete([$file]);
        $person->image()->delete();

        return response()->json($person->fresh(), 200);
    }

    public function list(module $module)
    {
        // Get module
        if (! $module) {
            return response()->json('Not found', 404);
        }

        $ids = $module->configuration['ids'];
        if (empty($ids)) {
            return response()->json([], 200);
        }

        $ids_ordered = implode(',', $ids);
        $persons = Person::whereIn('id', $ids)
            ->orderByRaw("FIELD(id, $ids_ordered)")
            ->get();

        return response()->json($persons, 200);
    }

    public function saveList(Module $module, StorePersonListRequest $request)
    {
        if (! $module) {
            return response()->json('Not found', 404);
        }

        // validate

        $order = $request->validated();

        // store

        $config = $module->configuration;
        $config['ids'] = $order['order'];

        $module->update(['configuration' => $config]);

        // clear cache

        Cache::forget(ListController::$CACHE_KEY_LIST.'_'.$module->id);

        // response

        return $this->list($module->fresh());
    }
}
