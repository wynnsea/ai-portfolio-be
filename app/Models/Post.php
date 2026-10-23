<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\CoverTone;
use App\Enums\PostCategory;
use App\Models\Concerns\HasAdminListing;
use App\Models\Concerns\HasPublicFiles;
use App\Models\Concerns\HasSlug;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'slug', 'title', 'category', 'status', 'date', 'read_time', 'excerpt', 'content',
    'title_jp', 'kanji', 'tone', 'cover_path', 'sort_order',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasAdminListing, HasFactory, HasPublicFiles, HasSlug;

    /**
     * Average reading speed used to estimate readTime.
     */
    public const WORDS_PER_MINUTE = 200;

    /**
     * Columns returned by the public list (everything except the content blocks).
     */
    public const SUMMARY_COLUMNS = [
        'id', 'slug', 'title', 'category', 'status', 'date', 'read_time', 'excerpt',
        'title_jp', 'kanji', 'tone', 'cover_path', 'sort_order', 'created_at', 'updated_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'Draft',
        'tone' => 'dark',
        'sort_order' => 0,
    ];

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            if ($post->status === ContentStatus::Published && $post->date === null) {
                $post->date = today();
            }

            $readTimeGiven = $post->isDirty('read_time') && filled($post->read_time);

            if (! $readTimeGiven && ($post->isDirty('content') || blank($post->read_time))) {
                $post->read_time = $post->estimateReadTime();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'category' => PostCategory::class,
            'tone' => CoverTone::class,
            'date' => 'date:Y-m-d',
            'content' => 'array',
            'sort_order' => 'integer',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', ContentStatus::Published);
    }

    /**
     * Estimate the reading time from the words in the content blocks, e.g. "5 menit baca".
     */
    public function estimateReadTime(): string
    {
        $words = collect($this->content ?? [])
            ->flatMap(fn (array $block): array => [$block['text'] ?? '', $block['code'] ?? '', ...($block['items'] ?? [])])
            ->sum(fn (string $text): int => Str::wordCount($text));

        $minutes = max(1, (int) ceil($words / self::WORDS_PER_MINUTE));

        return "{$minutes} menit baca";
    }

    public function searchableColumns(): array
    {
        return ['title', 'category', 'excerpt'];
    }

    public function filterableColumns(): array
    {
        return ['status' => 'status', 'category' => 'category'];
    }

    public function publicFileAttributes(): array
    {
        return ['cover_path'];
    }
}
