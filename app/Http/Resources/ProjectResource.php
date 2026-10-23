<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\FormatsContent;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
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
            'category' => $this->category,
            'year' => $this->year,
            'role' => $this->role,
            'duration' => $this->duration,
            'status' => $this->status->value,
            'summary' => $this->summary,
            'overview' => $this->overview,
            'challenge' => $this->challenge,
            'approach' => $this->approach ?? [],
            'results' => $this->results ?? [],
            'stack' => $this->stack ?? [],
            'links' => (object) ($this->links ?? []),
            ...$this->kanjiCover(),
            ...$this->coverImage(),
            ...$this->meta(),
        ];
    }
}
