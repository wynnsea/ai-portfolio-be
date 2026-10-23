<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\FormatsContent;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    use FormatsContent;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category->value,
            'status' => $this->status->value,
            'date' => $this->date?->toDateString(),
            'readTime' => $this->read_time,
            'excerpt' => $this->excerpt,
            'content' => $this->whenHas('content', fn () => $this->content ?? []),
            ...$this->kanjiCover(),
            ...$this->coverImage(),
            ...$this->meta(),
        ];
    }
}
