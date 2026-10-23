<?php

namespace App\Models;

use App\Models\Concerns\HasPublicFiles;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'brand', 'name', 'name_jp', 'role', 'description', 'location', 'email',
    'photo_path', 'cv_path', 'availability', 'socials', 'features',
])]
class Setting extends Model
{
    use HasPublicFiles;

    /**
     * The single settings row, created on first access.
     */
    public static function current(): self
    {
        return static::query()->oldest('id')->firstOrCreate();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'availability' => 'array',
            'socials' => 'array',
            'features' => 'array',
        ];
    }

    public function publicFileAttributes(): array
    {
        return ['photo_path', 'cv_path'];
    }
}
