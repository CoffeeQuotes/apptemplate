<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['file_name'])) {
            $filePath = Storage::disk('public')->path($data['file_name']);
            
            if (!file_exists($filePath)) {
                $this->halt();
                Notification::make()
                    ->title('File not found')
                    ->danger()
                    ->send();
                return $data;
            }

            $data['mime_type'] = mime_content_type($filePath);
            $data['size'] = filesize($filePath);
            
            // Validate file type matches selected type
            $isValidType = match($data['type']) {
                'image' => str_starts_with($data['mime_type'], 'image/'),
                'document' => in_array($data['mime_type'], [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.oasis.opendocument.text',
                    'text/plain'
                ]),
                'video' => str_starts_with($data['mime_type'], 'video/'),
                default => false,
            };
            
            if (!$isValidType) {
                $this->halt();
                Notification::make()
                    ->title('Invalid file type')
                    ->body("The uploaded file doesn't match the selected type.")
                    ->danger()
                    ->send();
                return $data;
            }

            // Set name if not provided
            if (empty($data['name'])) {
                $data['name'] = pathinfo($data['file_name'], PATHINFO_FILENAME);
            }

            // Ensure required fields have defaults
            $data['collection_name'] = $data['collection_name'] ?? 'default';
            $data['disk'] = $data['disk'] ?? 'public';
            $data['model_type'] = $data['model_type'] ?? 'App\Models\Media';
            $data['model_id'] = $data['model_id'] ?? 1;
        }
        
        return $data;
    }
}
