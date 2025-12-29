<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PropertyResource;
use App\Models\Property;
use App\Models\PropertyDraft;
use App\Services\ListingCreditService;
use App\Filament\Concerns\RedirectsToPricingOnCreditError;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

class CreateProperty extends CreateRecord
{
    use RedirectsToPricingOnCreditError;

    protected static string $resource = PropertyResource::class;
    protected ?PropertyDraft $draft = null;
    protected bool $isRestoringDraft = false;

    public function mount(): void
    {
        parent::mount();

        $this->isRestoringDraft = true;
        $this->restoreDraft();
        $this->isRestoringDraft = false;
        $this->redirectToDraftStepIfNeeded();
    }

    public function updated($name, $value): void
    {
        if ($this->isRestoringDraft) {
            return;
        }

        $this->saveDraft();
        $this->resetErrorBag();
    }

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
        $this->deleteDraft();

        if ($shouldPublish) {
            ListingCreditService::consumeListingCredits(auth()->user(), $property);
        }

        if ($property->is_featured) {
            ListingCreditService::consumeFeaturedCredits(auth()->user(), $property);
        }

        return $property;
    }

    private function restoreDraft(): void
    {
        $userId = auth()->id();
        if (!$userId) {
            return;
        }

        $agencyId = Filament::getTenant()?->id;
        $this->draft = PropertyDraft::query()
            ->where('user_id', $userId)
            ->where('agency_id', $agencyId)
            ->first();

        if (!$this->draft) {
            return;
        }

        $state = array_replace_recursive(
            $this->form->getRawState(),
            $this->draft->form_data ?? []
        );

        $this->form->fill($state);
    }

    private function saveDraft(): void
    {
        $userId = auth()->id();
        if (!$userId) {
            return;
        }

        $agencyId = Filament::getTenant()?->id;
        $state = $this->sanitizeDraftState($this->form->getRawState());

        $this->draft = PropertyDraft::updateOrCreate(
            [
                'user_id' => $userId,
                'agency_id' => $agencyId,
            ],
            [
                'form_data' => $state,
                'wizard_step' => request()->query($this->getWizardStepQueryStringKey()),
            ]
        );
    }

    private function deleteDraft(): void
    {
        if ($this->draft) {
            $this->draft->delete();
            $this->draft = null;
            return;
        }

        $userId = auth()->id();
        if (!$userId) {
            return;
        }

        PropertyDraft::query()
            ->where('user_id', $userId)
            ->where('agency_id', Filament::getTenant()?->id)
            ->delete();
    }

    private function sanitizeDraftState(array $state): array
    {
        $sanitized = [];

        foreach ($state as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeDraftState($value);
                continue;
            }

            if (is_object($value)) {
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function getWizardStepQueryStringKey(): string
    {
        return 'step';
    }

    private function redirectToDraftStepIfNeeded(): void
    {
        if (! $this->draft) {
            return;
        }

        $key = $this->getWizardStepQueryStringKey();
        if (filled(request()->query($key))) {
            return;
        }

        $step = $this->draft->wizard_step;
        if (blank($step)) {
            return;
        }

        $query = request()->query();
        $query[$key] = $step;

        $this->redirect(url()->current() . '?' . http_build_query($query), navigate: true);
    }
}
