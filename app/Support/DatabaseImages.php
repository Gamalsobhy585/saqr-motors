<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

/**
 * Assessment-only persistent image storage. Paths stay short (db-media/{uuid});
 * image bytes live in PostgreSQL, not on an ephemeral application filesystem.
 */
final class DatabaseImages
{
    private const PREFIX = 'db-media/';
    private const MAX_BYTES = 5 * 1024 * 1024;
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    public static function store(TemporaryUploadedFile $file): string
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false || $contents === '') {
            throw new RuntimeException('Could not read the uploaded image.');
        }

        $bytes = strlen($contents);

        if ($bytes > self::MAX_BYTES) {
            throw new RuntimeException('The uploaded image exceeds 5 MB.');
        }

        // Inspect the actual bytes; never trust a browser-supplied extension/MIME.
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);

        if (! in_array($mime, self::ALLOWED_MIMES, true) || getimagesizefromstring($contents) === false) {
            throw new RuntimeException('Only valid JPEG, PNG and WebP images are allowed.');
        }

        $id = (string) Str::uuid();

        DB::table('media_assets')->insert([
            'id' => $id,
            'mime_type' => $mime,
            'size_bytes' => $bytes,
            'content_base64' => base64_encode($contents),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return self::PREFIX . $id;
    }

    public static function isStoredPath(?string $path): bool
    {
        return is_string($path)
            && str_starts_with($path, self::PREFIX)
            && Str::isUuid(substr($path, strlen(self::PREFIX)));
    }

    public static function url(string $path): ?string
    {
        return self::isStoredPath($path)
            ? route('media.public', ['id' => substr($path, strlen(self::PREFIX))])
            : null;
    }

    /** @return array{name: string, size: int, type: string, url: string}|null */
    public static function fileInfo(string $path): ?array
    {
        if (! self::isStoredPath($path)) {
            return null;
        }

        $id = substr($path, strlen(self::PREFIX));

        $image = DB::table('media_assets')
            ->where('id', $id)
            ->first(['mime_type', 'size_bytes']);

        if (! $image) {
            return null;
        }

        $extension = match ($image->mime_type) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        return [
            'name' => "{$id}.{$extension}",
            'size' => (int) $image->size_bytes,
            'type' => $image->mime_type,
            'url' => self::url($path),
        ];
    }
}
