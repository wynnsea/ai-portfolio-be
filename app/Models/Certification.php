<?php

namespace App\Models;

use App\Enums\CertificationCategory;
use App\Enums\CoverTone;
use App\Models\Concerns\HasAdminListing;
use App\Models\Concerns\HasPublicFiles;
use Database\Factories\CertificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'issuer', 'category', 'year', 'score', 'note', 'credential_url',
    'title_jp', 'kanji', 'tone', 'cover_path', 'sort_order',
])]
class Certification extends Model
{
    /** @use HasFactory<CertificationFactory> */
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
            'category' => CertificationCategory::class,
            'tone' => CoverTone::class,
            'sort_order' => 'integer',
        ];
    }

    public function searchableColumns(): array
    {
        return ['title', 'issuer', 'category'];
    }

    public function filterableColumns(): array
    {
        return ['category' => 'category'];
    }

    public function publicFileAttributes(): array
    {
        return ['cover_path'];
    }
}
