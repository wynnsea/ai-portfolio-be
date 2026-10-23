<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class PostController extends ContentController
{
    protected string $model = Post::class;

    protected string $resource = PostResource::class;

    public function store(PostRequest $request): JsonResponse
    {
        return $this->storeFrom($request);
    }

    public function update(PostRequest $request, string $post): JsonResource
    {
        return $this->updateFrom($request, $post);
    }
}
