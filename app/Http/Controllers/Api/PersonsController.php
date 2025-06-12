<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePerson;
use App\Http\Requests\StorePersonListRequest;
use App\Models\File;
use App\Models\Module;
use App\Models\Person;
use Illuminate\Support\Facades\Storage;

class PersonsController extends Controller
{
    /**
     * @var int
     */
    protected $itemsPerPage = 10;

    public function __construct()
    {
        if (request()->query('itemsPerPage')) {
            $this->itemsPerPage = request()->query('itemsPerPage');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Person::orderBy('name', 'asc')->paginate($this->itemsPerPage);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePerson $request)
    {
        $person = Person::create($request->all());

        return response()->json($person, 201);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        return response()->json($person, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(StorePerson $request, Person $person)
    {
        $person->update($request->all());

        return response()->json($person, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        // Delete attachments
        foreach ($person->image()->get() as $file) {
            Storage::disk(File::$DISK_NAME)->delete(str_replace('/upload', '', $file->file));
            $file->delete();
        }

        // Delete person
        $person->delete();

        return response()->json(null, 204);
    }

    /**
     * Attaches a file to a specific event.
     *
     * @return \Illuminate\Http\Response
     */
    public function attachFile(Person $person)
    {
        request()->validate([
            'file' => 'required',
        ]);

        $file = request()->file('file')->store('/', File::$DISK_NAME);

        $person->image()->create([
            'file' => '/upload/'.$file,
            'title' => request()->file('file')->getClientOriginalName(),
        ]);

        return response()->json($person->fresh(), 200);
    }

    public function list($module_id)
    {
        // Get module
        $module = Module::find($module_id);
        if (! $module) {
            return response()->json('Not found', 404);
        }

        $ids = $module->configuration['ids'];
        if (empty($ids)) {
            return response()->json([], 200);
        }

        $persons = Person::whereIn('id', $ids)->get();
        $persons = $persons->sortBy(function ($person) use ($ids) {
            return array_search($person->id, $ids);
        });

        return response()->json($persons->values(), 200);
    }

    public function saveList(Module $module, StorePersonListRequest $request)
    {
        $module->configuration = ['ids' => $request->order];
        $module->save();

        return response()->json($module, 200);
    }
}
