<?php

namespace App\Support;

final class MissingImagePaths
{
    /**
     * Clear an unavailable file from form state only.
     *
     * Existing database values are left unchanged until the user saves.
     * Valid database images, seeded public assets and existing public-disk
     * files remain untouched. Remote URLs are preserved for compatibility.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function clear(
        array $data,
        string $field,
        string $staticDirectory,
    ): array {
        $path = $data[$field] ?? null;

        if (
            ! is_string($path)
            || blank($path)
            || filter_var($path, FILTER_VALIDATE_URL)
        ) {
            return $data;
        }

        $resolve = ResolvesPublicImages::forDirectory($staticDirectory);

        if ($resolve($path) === null) {
            $data[$field] = null;
        }

        return $data;
    }
}
