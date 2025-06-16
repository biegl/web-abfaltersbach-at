<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $config = json_encode($request['configuration']);

        $module = Module::create([
            'type' => 'list',
            'configuration' => $config,
        ]);

        return $module;
    }

    public function saveList(Request $request, $id)
    {
        // Get module
        $module = Module::find($id);
        if (! $module) {
            return response()->json('Not found', 404);
        }

        $config = $module->configuration;
        $config['ids'] = $request['order'];
        $module->update(['configuration' => $config]);

        return $module;
    }
}
