<?php

namespace App\Filament\Agency\Resources\PropertyResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Agency\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Property;
use App\Services\ListingCreditService;
use Filament\Facades\Filament;
use App\Filament\Concerns\RedirectsToPricingOnCreditError;
use Illuminate\Validation\ValidationException;

class EditProperty extends EditRecord
{
    use RedirectsToPricingOnCreditError;
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Property::applyListingPackageData($data, $this->record);
    }

    protected function handleRecordUpdate($record, array $data): Property
    {
        $agency = Filament::getTenant();
        if (!$record->is_published && !empty($data['is_published'])) {
            try {
                ListingCreditService::assertHasListingCredits($agency);
                ListingCreditService::consumeListingCredits($agency, $record);
            } catch (ValidationException $exception) {
                $this->redirectToPricingForCredits($exception);
            }
        }
        if (!$record->is_featured && !empty($data['is_featured'])) {
            try {
                ListingCreditService::assertHasFeaturedCredits($agency);
                ListingCreditService::consumeFeaturedCredits($agency, $record);
            } catch (ValidationException $exception) {
                $this->redirectToPricingForCredits($exception);
            }
        }

        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
