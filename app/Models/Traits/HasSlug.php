<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name ?? $model->title);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') || $model->isDirty('title')) {
                $model->slug = Str::slug($model->name ?? $model->title);
            }
        });
    }
} 