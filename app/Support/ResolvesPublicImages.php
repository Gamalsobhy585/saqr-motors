
<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Storage;

final class ResolvesPublicImages
{
    public static function forDirectory(string $staticDirectory): Closure
    {
        return static function (string $file) use ($staticDirectory): ?array {
            $disk = Storage::disk('public');

            // Existing uploaded file takes priority.
            if ($disk->exists($file)) {
                return [
                    'name' => basename($file),
                    'size' => $disk->size($file),
                    'type' => $disk->mimeType($file),
                    'url' => $disk->url($file),
                ];
            }

            // Direct static path, for example images/services/oil.jpg.
            if (str_starts_with($file, 'images/')) {
                $staticPath = $file;
            } else {
                // Seeders store paths like motorcycles/images/cb650r.jpg.
                $staticPath = trim($staticDirectory, '/') . '/' . basename($file);
            }

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
