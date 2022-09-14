<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralSettingsRequest;
use App\Http\Resources\GeneralSettingsResource;
use App\Models\GeneralSettings;

class GeneralSettingsController extends Controller
{
    public function show(GeneralSettings $settings)
    {
        return new GeneralSettingsResource($settings);
    }

    public function update(GeneralSettingsRequest $request, GeneralSettings $settings)
    {
        $validated = $request->validated(null, null);

        $settings->fill($validated);
        $settings->save();

        return new GeneralSettingsResource($settings);
    }
}
