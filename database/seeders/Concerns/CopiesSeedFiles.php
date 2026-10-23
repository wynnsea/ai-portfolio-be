<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait CopiesSeedFiles
{
    /**
     * Copy a file from database/seeders/files to the public disk (if missing)
     * and return its path on the disk.
     */
    protected function seedFile(string $path): string
    {
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, file_get_contents(database_path("seeders/files/{$path}")));
        }

        return $path;
    }
}
