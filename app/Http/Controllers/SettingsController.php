<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function show(): SettingResource
    {
        return new SettingResource(Setting::current());
    }
}
