<?php

namespace App\Http\Controllers;

use App\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $config = json_encode($request['configuration']);
        return Module::create([
            'type' => 'list',
            'configuration' => $config,
        ]);
    }

    public function saveList(Module $module, Request $request)
    {
        // Get module
        if (!$module) {
            return response()->json('Not found', 404);
        }

        $config = $module->configuration;
        $config['ids'] = $request['order'];
        $module->update(['configuration' => $config]);

        return $module->fresh();
    }
}
