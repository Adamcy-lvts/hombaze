<?php

namespace App\Filament\Agent\Resources\PropertyResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Agent\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Property;
use App\Services\ListingCreditService;
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
        $owner = $record->agency_id ? $record->agency : auth()->user();
        if (!$record->is_published && !empty($data['is_published'])) {
            try {
                ListingCreditService::assertHasListingCredits($owner);
                ListingCreditService::consumeListingCredits($owner, $record);
            } catch (ValidationException $exception) {
                $this->redirectToPricingForCredits($exception);
            }
        }
        if (!$record->is_featured && !empty($data['is_featured'])) {
            try {
                ListingCreditService::assertHasFeaturedCredits($owner);
                ListingCreditService::consumeFeaturedCredits($owner, $record);
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
