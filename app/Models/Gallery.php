<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Gallery extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'template',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($gallery) {
            if (empty($gallery->slug)) {
                $gallery->slug = Str::slug($gallery->name);
            }
        });
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class)
            ->withPivot('sort_order')
            ->orderBy('sort_order');
    }

    public function getImagesAttribute()
    {
        return $this->media()->where('type', 'image')->get();
    }

    public function getVideosAttribute()
    {
        return $this->media()->where('type', 'video')->get();
    }

    public function getDocumentsAttribute()
    {
        return $this->media()->where('type', 'document')->get();
    }

    public function getTemplateOptionsAttribute(): array
    {
        return [
            'grid' => [
                'name' => 'Grid',
                'description' => 'Display media in a responsive grid layout',
                'columns' => [2, 3, 4, 6],
                'default_columns' => 4,
            ],
            'masonry' => [
                'name' => 'Masonry',
                'description' => 'Pinterest-style masonry layout',
                'columns' => [2, 3, 4, 5],
                'default_columns' => 3,
            ],
            'slider' => [
                'name' => 'Slider',
                'description' => 'Carousel/slideshow layout',
                'options' => [
                    'autoplay' => true,
                    'arrows' => true,
                    'dots' => true,
                ],
            ],
        ];
    }
}
