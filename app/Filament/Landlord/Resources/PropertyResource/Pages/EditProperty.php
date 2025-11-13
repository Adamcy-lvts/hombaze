<?php

namespace App\Filament\Landlord\Resources\PropertyResource\Pages;

use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;
use App\Filament\Landlord\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Handle the record update with proper data processing
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Process the data before saving
        $data = $this->processPropertyData($data);
        
        // Update the record
        $record->update($data);
        
        // Handle feature relationships
        if (isset($data['features'])) {
            $record->features()->sync($data['features']);
        }
        
        // Show success notification
        Notification::make()
            ->title('Property updated successfully')
            ->success()
            ->send();
            
        return $record;
    }

    /**
     * Process property data before saving
     */
    private function processPropertyData(array $data): array
    {
        // Generate slug if title changed and slug not provided
        if (isset($data['title']) && (empty($data['slug']) || $data['slug'] === $this->record->slug)) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Ensure boolean values are properly set
        $data['is_published'] = $data['is_published'] ?? false;
        $data['is_featured'] = $data['is_featured'] ?? false;
        $data['is_verified'] = $data['is_verified'] ?? false;

        // Set published_at timestamp based on is_published status
        if ($data['is_published'] && !$this->record->published_at) {
            $data['published_at'] = now();
        } elseif (!$data['is_published']) {
            $data['published_at'] = null;
        }

        return $data;
    }
}
