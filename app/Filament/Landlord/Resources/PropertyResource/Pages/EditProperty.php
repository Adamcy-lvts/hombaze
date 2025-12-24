<?php

namespace App\Filament\Landlord\Resources\PropertyResource\Pages;

use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;
use App\Filament\Landlord\Resources\PropertyResource;
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
    use RedirectsToPricingOnCreditError;
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
        $data = Property::applyListingPackageData($data, $this->record);

        if (!$this->record->is_published && !empty($data['is_published'])) {
            try {
                ListingCreditService::assertHasListingCredits(auth()->user());
                ListingCreditService::consumeListingCredits(auth()->user(), $this->record);
            } catch (ValidationException $exception) {
                $this->redirectToPricingForCredits($exception);
            }
        }

        if (!$this->record->is_featured && !empty($data['is_featured'])) {
            try {
                ListingCreditService::assertHasFeaturedCredits(auth()->user());
                ListingCreditService::consumeFeaturedCredits(auth()->user(), $this->record);
            } catch (ValidationException $exception) {
                $this->redirectToPricingForCredits($exception);
            }
        }

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
