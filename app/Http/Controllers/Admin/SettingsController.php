<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function show(): SettingResource
    {
        return new SettingResource(Setting::current());
    }

    /**
     * Partial update: nested "availability" and "features" are merged with the stored values.
     */
    public function update(UpdateSettingsRequest $request): SettingResource
    {
        $setting = Setting::current();
        $attributes = $request->attributesForModel();

        foreach (['availability', 'features'] as $group) {
            if (array_key_exists($group, $attributes)) {
                $attributes[$group] = [...($setting->{$group} ?? []), ...$attributes[$group]];
            }
        }

        $setting->update($attributes);

        return new SettingResource($setting);
    }
}
