<?php

namespace App\Http\Resources\Concerns;

use App\Support\PublicFile;

/**
 * Shared fields for content resources: the kanji cover, sort order and timestamps.
 */
trait FormatsContent
{
    /**
     * @return array{titleJp: ?string, kanji: ?string, tone: ?string}
     */
    protected function kanjiCover(): array
    {
        return [
            'titleJp' => $this->title_jp,
            'kanji' => $this->kanji,
            'tone' => $this->tone?->value,
        ];
    }

    /**
     * @return array{coverUrl: ?string}
     */
    protected function coverImage(): array
    {
        return ['coverUrl' => PublicFile::url($this->cover_path)];
    }

    /**
     * @return array{sortOrder: int, createdAt: ?string, updatedAt: ?string}
     */
    protected function meta(): array
    {
        return [
            'sortOrder' => (int) $this->sort_order,
            'createdAt' => $this->created_at?->toIso8601ZuluString(),
            'updatedAt' => $this->updated_at?->toIso8601ZuluString(),
        ];
    }
}
