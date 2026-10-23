<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\CoverTone;
use App\Models\Concerns\HasAdminListing;
use App\Models\Concerns\HasPublicFiles;
use App\Models\Concerns\HasSlug;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'slug', 'title', 'category', 'year', 'role', 'duration', 'status', 'summary', 'overview', 'challenge',
    'approach', 'results', 'stack', 'links', 'title_jp', 'kanji', 'tone', 'cover_path', 'sort_order',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasAdminListing, HasFactory, HasPublicFiles, HasSlug;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'Draft',
        'tone' => 'dark',
        'sort_order' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'tone' => CoverTone::class,
            'approach' => 'array',
            'results' => 'array',
            'stack' => 'array',
            'links' => 'array',
            'sort_order' => 'integer',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', ContentStatus::Published);
    }

    public function searchableColumns(): array
    {
        return ['title', 'category', 'stack'];
    }

    public function filterableColumns(): array
    {
        return ['status' => 'status'];
    }

    public function publicFileAttributes(): array
    {
        return ['cover_path'];
    }
}
