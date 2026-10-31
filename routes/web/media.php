<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Public dealership images; media identifiers are random UUIDs.
Route::get('/media/{id}', function (string $id) {
    $image = DB::table('media_assets')
        ->where('id', $id)
        ->first(['mime_type', 'content_base64']);

    abort_if($image === null, 404);

    $contents = base64_decode($image->content_base64, true);

    abort_if($contents === false, 404);

    return response($contents, 200, [
        'Content-Type' => $image->mime_type,
        'Content-Length' => (string) strlen($contents),
        'Cache-Control' => 'public, max-age=3600',
        'X-Content-Type-Options' => 'nosniff',
    ]);
})->whereUuid('id')->name('media.public');
