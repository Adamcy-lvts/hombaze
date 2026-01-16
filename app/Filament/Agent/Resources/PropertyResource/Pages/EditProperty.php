<?php

namespace App\Filament\Agent\Resources\PropertyResource\Pages;

use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;
use App\Filament\Agent\Resources\PropertyResource;
use App\Models\Property;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Services\ListingCreditService;
use App\Filament\Concerns\RedirectsToPricingOnCreditError;
use Illuminate\Validation\ValidationException;

class EditProperty extends EditRecord
{
    // use RedirectsToPricingOnCreditError; // Commented out to avoid potential trait missing error if Agent scope differs
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
        // Simple processing for now to ensure stability
        if (isset($data['title']) && (empty($data['slug']) || $data['slug'] === $this->record->slug)) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $data;
    }
}
