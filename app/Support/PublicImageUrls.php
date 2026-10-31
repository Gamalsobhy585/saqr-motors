<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class PublicImageUrls
{
    public static function resolve(?string $path, ?string $staticDirectory = null): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (DatabaseImages::isStoredPath($path)) {
            return DatabaseImages::url($path);
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return is_file(public_path($path)) ? asset($path) : null;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            return $disk->url($path);
        }

        // Preserve the committed assessment images even when seeded copies
        // in storage/app/public are no longer available after a Cloud deploy.
        if ($staticDirectory !== null) {
            $staticPath = trim($staticDirectory, '/') . '/' . basename($path);

            if (is_file(public_path($staticPath))) {
                return asset($staticPath);
            }
        }

        return null;
    }
}
