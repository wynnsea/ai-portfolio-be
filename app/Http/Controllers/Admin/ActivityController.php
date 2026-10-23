<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityController extends ContentController
{
    protected string $model = Activity::class;

    protected string $resource = ActivityResource::class;

    public function store(ActivityRequest $request): JsonResponse
    {
        return $this->storeFrom($request);
    }

    public function update(ActivityRequest $request, string $activity): JsonResource
    {
        return $this->updateFrom($request, $activity);
    }
}
