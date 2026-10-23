<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\FormatsContent;
use App\Models\Activity;
use App\Support\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
class ActivityResource extends JsonResource
{
    use FormatsContent;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'role' => $this->role,
            'organizer' => $this->organizer,
            'category' => $this->category,
            'year' => $this->year,
            'description' => $this->description,
            'photos' => array_map(PublicFile::url(...), $this->photos ?? []),
            'focus' => $this->focus,
            ...$this->kanjiCover(),
            ...$this->meta(),
        ];
    }
}
