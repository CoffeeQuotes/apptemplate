<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $product = $this->record;
        $images = $this->data['images'] ?? [];
        
        // Create product images with numeric index for sort_order
        foreach ($images as $index => $path) {
            $product->images()->create([
                'path' => $path,  // Remove 'products/' prefix here since it's already added by directory()
                'sort_order' => (int) $index,
                'is_primary' => $index === 0,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 