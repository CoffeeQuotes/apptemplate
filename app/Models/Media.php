<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Illuminate\Support\Facades\Storage;

class Media extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'alt_text',
        'folder',
        'type',
        'caption',
        'description',
        'custom_properties',
        'model_type',
        'model_id',
        'collection_name',
        'file_name',
        'disk',
        'mime_type',
        'size'
    ];

    protected $casts = [
        'custom_properties' => 'array',
        'manipulations' => 'array',
        'generated_conversions' => 'array',
        'responsive_images' => 'array',
    ];

    public function getUrlAttribute(): string
    {
        if ($this->disk === 'public') {
            return asset('storage/' . $this->file_name);
        }
        return Storage::disk($this->disk)->url($this->file_name);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->file_name);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->type === 'image') {
            return $this->url;
        }
        return asset('images/placeholder.png');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($media) {
            if (empty($media->model_type)) {
                $media->model_type = 'App\Models\Media';
                $media->model_id = 1;
            }
            
            // Set defaults for required fields
            $media->disk = $media->disk ?? 'public';
            $media->size = $media->size ?? 0;
            $media->name = $media->name ?? pathinfo($media->file_name, PATHINFO_FILENAME);
            
            // Ensure JSON fields are arrays and handle null values
            $media->manipulations = is_null($media->manipulations) ? [] : (array) $media->manipulations;
            $media->generated_conversions = is_null($media->generated_conversions) ? [] : (array) $media->generated_conversions;
            $media->responsive_images = is_null($media->responsive_images) ? [] : (array) $media->responsive_images;
            $media->custom_properties = is_null($media->custom_properties) ? [] : (array) $media->custom_properties;
            
            $media->collection_name = $media->collection_name ?? 'default';

            // Set mime type if not set
            if (empty($media->mime_type) && !empty($media->file_name)) {
                $media->mime_type = Storage::disk($media->disk)->mimeType($media->file_name);
            }
        });
    }

    public function registerMediaConversions(SpatieMedia $media = null): void 
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->performOnCollections('images');

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(800)
            ->performOnCollections('images');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.text',
                'text/plain'
            ]);

        $this->addMediaCollection('videos')
            ->acceptsMimeTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo']);
    }
}
