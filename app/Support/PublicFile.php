<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Converts between file paths stored in the database (relative to the public disk)
 * and the absolute URLs exposed by the API.
 */
class PublicFile
{
    /**
     * Normalize an incoming URL or path into a path relative to the public disk.
     * External URLs (other hosts) are kept as-is.
     */
    public static function path(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $diskUrl = rtrim(Storage::disk('public')->url(''), '/').'/';

        if (Str::startsWith($value, $diskUrl)) {
            return Str::after($value, $diskUrl);
        }

        if (Str::startsWith($value, '/storage/')) {
            return Str::after($value, '/storage/');
        }

        if (self::isExternal($value)) {
            return $value;
        }

        return ltrim($value, '/');
    }

    /**
     * Turn a stored path into an absolute URL.
     */
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return self::isExternal($path) ? $path : Storage::disk('public')->url($path);
    }

    /**
     * Delete a stored file from the public disk. External URLs are ignored.
     */
    public static function delete(?string $path): void
    {
        if ($path === null || $path === '' || self::isExternal($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private static function isExternal(string $value): bool
    {
        return Str::startsWith($value, ['http://', 'https://']);
    }
}
