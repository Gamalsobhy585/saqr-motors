<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Storage;

final class ResolvesPublicImages
{
    public static function forDirectory(string $staticDirectory): Closure
    {
        return static function (string $file) use ($staticDirectory): ?array {
            if (DatabaseImages::isStoredPath($file)) {
                return DatabaseImages::fileInfo($file);
            }

            $disk = Storage::disk('public');

            if ($disk->exists($file)) {
                return [
                    'name' => basename($file),
                    'size' => $disk->size($file),
                    'type' => $disk->mimeType($file),
                    'url' => $disk->url($file),
                ];
            }

            $staticPath = str_starts_with($file, 'images/')
                ? $file
                : trim($staticDirectory, '/') . '/' . basename($file);

            if (! is_file(public_path($staticPath))) {
                return null;
            }

            return [
                'name' => basename($staticPath),
                'size' => filesize(public_path($staticPath)),
                'type' => mime_content_type(public_path($staticPath)) ?: 'image/jpeg',
                'url' => asset($staticPath),
            ];
        };
    }
}
