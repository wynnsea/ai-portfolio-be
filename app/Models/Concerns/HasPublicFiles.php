<?php

namespace App\Models\Concerns;

use App\Support\PublicFile;
use Illuminate\Support\Arr;

/**
 * Deletes files from the public disk when they are replaced or the model is deleted.
 *
 * The model must implement publicFileAttributes(), returning the attributes that hold
 * a file path (string) or a list of file paths (array cast).
 */
trait HasPublicFiles
{
    public static function bootHasPublicFiles(): void
    {
        static::updated(function (self $model): void {
            foreach ($model->publicFileAttributes() as $attribute) {
                if (! $model->wasChanged($attribute)) {
                    continue;
                }

                $removed = array_diff(
                    Arr::wrap($model->getOriginal($attribute)),
                    Arr::wrap($model->getAttribute($attribute)),
                );

                array_map(PublicFile::delete(...), $removed);
            }
        });

        static::deleted(function (self $model): void {
            foreach ($model->publicFileAttributes() as $attribute) {
                array_map(PublicFile::delete(...), Arr::wrap($model->getAttribute($attribute)));
            }
        });
    }

    /**
     * @return list<string>
     */
    abstract public function publicFileAttributes(): array;
}
