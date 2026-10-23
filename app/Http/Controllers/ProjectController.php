<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection(Project::query()->published()->ordered()->get());
    }

    public function show(string $slug): ProjectResource
    {
        return new ProjectResource(Project::query()->published()->where('slug', $slug)->firstOrFail());
    }
}
