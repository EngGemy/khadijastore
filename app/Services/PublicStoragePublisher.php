<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

/**
 * Mirror storage/app/public → public/storage (real directory).
 *
 * Enhance/LiteSpeed serves static files only from real paths under public/.
 * storage:link symlinks are not reliable; uploads must be copied to public/storage.
 */
class PublicStoragePublisher
{
    public static function publishAll(): int
    {
        $source = storage_path('app/public');

        if (! File::isDirectory($source)) {
            return 0;
        }

        $published = 0;

        foreach (File::allFiles($source) as $file) {
            if (static::publishPath($file->getRelativePathname())) {
                $published++;
            }
        }

        return $published;
    }

    public static function publishPath(string $relativePath): bool
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $source = storage_path('app/public/'.$relativePath);
        $dest = public_path('storage/'.$relativePath);

        if (! File::exists($source) || ! File::isFile($source)) {
            return false;
        }

        File::ensureDirectoryExists(dirname($dest));
        File::copy($source, $dest);

        return true;
    }
}
