<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ActivityResource::collection(Activity::query()->ordered()->get());
    }
}
