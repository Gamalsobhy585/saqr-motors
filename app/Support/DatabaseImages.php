<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

final class DatabaseImages
{
    public static function store(TemporaryUploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if (! in_array($mime, [
            'image/jpeg',
            'image/png',
            'image/webp',
        ], true)) {
            throw new RuntimeException('Unsupported image type.');
        }

        $contents = file_get_contents($file->getRealPath());

        if ($contents === false || strlen($contents) > 5 * 1024 * 1024) {
            throw new RuntimeException('Invalid image size.');
        }

        $id = (string) Str::uuid();

        DB::table('media_assets')->insert([
            'id' => $id,
            'mime_type' => $mime,
            'size_bytes' => strlen($contents),
            'content_base64' => base64_encode($contents),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return "db-media/{$id}";
    }

    public static function url(string $path): ?string
    {
        $id = Str::after($path, 'db-media/');

        return Str::isUuid($id)
            ? route('media.public', ['id' => $id])
            : null;
    }

    public static function fileInfo(string $path): ?array
    {
        $id = Str::after($path, 'db-media/');

        if (! Str::isUuid($id)) {
            return null;
        }

        $image = DB::table('media_assets')
            ->where('id', $id)
            ->first(['mime_type', 'size_bytes']);

        if (! $image) {
            return null;
        }

        return [
            'name' => "{$id}." . match ($image->mime_type) {
                'image/png' => 'png',
                'image/webp' => 'webp',
                default => 'jpg',
            },
            'size' => $image->size_bytes,
            'type' => $image->mime_type,
            'url' => self::url($path),
        ];
    }
}
