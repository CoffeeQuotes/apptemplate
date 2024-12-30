<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Notification;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['file_name'])) {
            $filePath = storage_path('app/public/' . $data['file_name']);
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
                    ->danger()
                    ->send();
                return $data;
            }
        }
        
        return $data;
    }
}
