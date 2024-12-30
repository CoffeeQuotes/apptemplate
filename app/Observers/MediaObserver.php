<?php

namespace App\Observers;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaObserver
{
    public function creating(Media $media): void 
    {
        // Only generate alt text if neither alt_text nor custom_properties['alt'] is provided
        if (empty($media->alt_text) && empty($media->custom_properties['alt'])) {
            $fileName = is_array($media->file_name) 
                ? ($media->file_name[0] ?? '') 
                : $media->file_name;
                
            $media->alt_text = str_replace(
                ['-', '_'], 
                ' ', 
                pathinfo($fileName, PATHINFO_FILENAME)
            );
        }

        // If alt_text is provided but custom_properties['alt'] exists, remove it
        if (!empty($media->alt_text) && isset($media->custom_properties['alt'])) {
            unset($media->custom_properties['alt']);
        }
    }    
    
    public function saving(Media $media): void
    {
        // Ensure custom_properties is an array
        if (!is_array($media->custom_properties)) {
            $media->custom_properties = [];
        }

        // Remove any auto-generated alt from custom_properties if alt_text is provided
        if (!empty($media->alt_text) && isset($media->custom_properties['alt'])) {
            unset($media->custom_properties['alt']);
        }
    }

    public function deleted(Media $media): void
    {
       // Clean up any associated files or references
        // This is handled automatically by Spatie Media Library
    }
}
