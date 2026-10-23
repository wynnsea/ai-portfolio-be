<?php

namespace App\Models;

use App\Enums\CoverTone;
use App\Models\Concerns\HasAdminListing;
use App\Models\Concerns\HasPublicFiles;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'role', 'organizer', 'category', 'year', 'description', 'photos', 'focus',
    'title_jp', 'kanji', 'tone', 'sort_order',
])]
class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasAdminListing, HasFactory, HasPublicFiles;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'tone' => 'dark',
        'sort_order' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tone' => CoverTone::class,
            'photos' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function searchableColumns(): array
    {
        return ['title', 'role', 'organizer'];
    }

    public function filterableColumns(): array
    {
        return ['category' => 'category'];
    }

    public function publicFileAttributes(): array
    {
        return ['photos'];
    }
}
