<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait TracksEditors
{
    protected static function bootTracksEditors(): void
    {
        static::creating(function (Model $model): void {
            if (! auth()->check()) {
                return;
            }

            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function (Model $model): void {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
