<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PropertyResource;
use App\Models\Property;
use App\Services\ListingCreditService;
use App\Filament\Concerns\RedirectsToPricingOnCreditError;
use Illuminate\Validation\ValidationException;

class CreateProperty extends CreateRecord
{
    use RedirectsToPricingOnCreditError;
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Property::applyListingPackageData($data);
    }

    protected function handleRecordCreation(array $data): Property
    {
        $shouldPublish = array_key_exists('is_published', $data) ? (bool) $data['is_published'] : true;
        try {
            if ($shouldPublish) {
                ListingCreditService::assertHasListingCredits(auth()->user());
            }

            if (!empty($data['is_featured'])) {
                ListingCreditService::assertHasFeaturedCredits(auth()->user());
            }
        } catch (ValidationException $exception) {
            $this->redirectToPricingForCredits($exception);
        }

        $property = static::getModel()::create($data);

        if ($shouldPublish) {
            ListingCreditService::consumeListingCredits(auth()->user(), $property);
        }

        if ($property->is_featured) {
            ListingCreditService::consumeFeaturedCredits(auth()->user(), $property);
        }

        return $property;
    }
}
