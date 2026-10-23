<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * Published posts, newest first, without their content blocks.
     */
    public function index(): AnonymousResourceCollection
    {
        $posts = Post::query()
            ->select(Post::SUMMARY_COLUMNS)
            ->published()
            ->orderByDesc('date')
            ->latest('id')
            ->get();

        return PostResource::collection($posts);
    }

    public function show(string $slug): PostResource
    {
        return new PostResource(Post::query()->published()->where('slug', $slug)->firstOrFail());
    }
}
