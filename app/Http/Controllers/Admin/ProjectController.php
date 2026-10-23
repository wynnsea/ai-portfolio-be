<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectController extends ContentController
{
    protected string $model = Project::class;

    protected string $resource = ProjectResource::class;

    public function store(ProjectRequest $request): JsonResponse
    {
        return $this->storeFrom($request);
    }

    public function update(ProjectRequest $request, string $project): JsonResource
    {
        return $this->updateFrom($request, $project);
    }
}
