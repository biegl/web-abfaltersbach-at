<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePerson;
use Illuminate\Support\Facades\Storage;
use App\File;
use App\Person;

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
        return response()->json($person, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Person  $person
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
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(StorePerson $request, Person $person)
    {
        $person->update($request->validated());
        return response()->json($person, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person $person
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

        return response()->json(null, 204);
    }

    /**
     * Attaches a file to a specific event.
     * @param  \App\Person $person
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
     * @param  \App\Person $person
     * @return \Illuminate\Http\Response
     */
    public function deleteFile(Person $person, File $file)
    {
        Storage::delete([$file]);
        $person->image()->delete();

        return response()->json($person->fresh(), 200);
    }
}
