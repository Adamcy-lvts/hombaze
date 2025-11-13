<?php

namespace App\Livewire\Customer;

use Exception;
use Filament\Actions\Action;
use App\Livewire\Forms\CustomerPreferencesForm;
use App\Models\PropertySubtype;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SettingsPreferences extends Component
{
    public CustomerPreferencesForm $form;

    public array $apartmentSubtypes = [];
    public array $houseSubtypes = [];
    public array $landSizes = [];

    public function mount()
    {
        $this->loadPropertySubtypes();
        $this->loadPreferencesData();
    }

    protected function loadPropertySubtypes(): void
    {
        // Load most relevant apartment subtypes for Nigeria (5-6 options)
        $this->apartmentSubtypes = PropertySubtype::where('property_type_id', 1)
            ->whereIn('name', [
                'Mini Flat',
                'One Bedroom Apartment',
                'Two Bedroom Apartment',
                'Three Bedroom Apartment',
                'Penthouse',
                'Serviced Apartment',
            ])
            ->orderByRaw("FIELD(name, 'Mini Flat', 'One Bedroom Apartment', 'Two Bedroom Apartment', 'Three Bedroom Apartment', 'Penthouse', 'Serviced Apartment')")
            ->get(['id', 'name'])
            ->toArray();

        // Load most relevant Nigerian house types (6 options)
        $this->houseSubtypes = PropertySubtype::where('property_type_id', 2)
            ->whereIn('name', [
                '2 Bedroom Bungalow',
                '3 Bedroom Bungalow',
                '3 Bedroom Duplex',
                '4 Bedroom Duplex',
                '3 Bedroom Terrace',
                '4 Bedroom Detached',
            ])
            ->orderByRaw("FIELD(name, '2 Bedroom Bungalow', '3 Bedroom Bungalow', '3 Bedroom Duplex', '4 Bedroom Duplex', '3 Bedroom Terrace', '4 Bedroom Detached')")
            ->get(['id', 'name'])
            ->toArray();

        // Define most common land sizes in Nigeria (5 options)
        $this->landSizes = [
            ['id' => 'half_plot', 'name' => 'Half Plot (300sqm)'],
            ['id' => '1_plot', 'name' => '1 Plot (600sqm)'],
            ['id' => '2_plots', 'name' => '2 Plots'],
            ['id' => '3_plots', 'name' => '3 Plots'],
            ['id' => 'acreage', 'name' => 'Acreage'],
        ];
    }

    public function getAvailablePropertyCategoriesProperty()
    {
        return $this->form->getAvailablePropertyCategories();
    }

    protected function loadPreferencesData(): void
    {
        $user = auth()->user();
        $profile = $user->customerProfile;

        if ($profile) {
            $this->form->setCustomerProfile($profile);
        }
    }

    public function updatedFormPreferredLocationState($value)
    {
        $this->form->preferred_location_city = null;
        $this->form->preferred_location_area = null;
    }

    public function updatedFormPreferredLocationCity($value)
    {
        $this->form->preferred_location_area = null;
    }

    public function updatedFormInterestedIn($value)
    {
        // Clear property categories that are no longer valid based on new interests
        $availableCategories = collect($this->form->getAvailablePropertyCategories())->pluck('value')->toArray();
        $currentCategories = $this->form->property_categories;

        // Filter out categories that are no longer available
        $this->form->property_categories = array_intersect($currentCategories, $availableCategories);

        $this->form->autoSave();
        $this->dispatch('preference-saved');
    }

    public function updatedFormPropertyCategories($value)
    {
        $this->form->autoSave();
        $this->dispatch('preference-saved');
    }

    public function updatedFormApartmentSubtypes($value)
    {
        $this->form->autoSave();
        $this->dispatch('preference-saved');
    }

    public function updatedFormHouseSubtypes($value)
    {
        $this->form->autoSave();
        $this->dispatch('preference-saved');
    }

    public function updatedFormLandSizes($value)
    {
        $this->form->autoSave();
        $this->dispatch('preference-saved');
    }

    public function updatedFormShopSelected($value)
    {
        $this->form->autoSave();
        $this->dispatch('preference-saved');
    }

    public function updatedFormBudgets($value)
    {
        $this->form->autoSave();
        $this->dispatch('preference-saved');
    }

    public function savePreferences(): void
    {
        try {
            $this->form->store();

            // Send success notification
            $this->sendSuccessNotification();

            // Add session message for toast notification
            session()->flash('success', 'Your preferences have been saved successfully! 🎉');

            // Trigger recommendation refresh
            $this->dispatch('preferences-updated');
            $this->dispatch('preferences-saved');

        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Saving Preferences')
                ->body('Failed to update preferences: '.$e->getMessage())
                ->persistent()
                ->send();

            // Add session message for toast notification
            session()->flash('error', 'There was an error saving your preferences. Please try again.');

            // Dispatch error event
            $this->dispatch('preferences-error', ['message' => 'There was an error saving your preferences. Please try again.']);

            Log::error('Preferences update error: '.$e->getMessage(), [
                'user_id' => auth()->id(),
                'form_data' => $this->form->all() ?? null,
            ]);
        }
    }

    protected function sendSuccessNotification(): void
    {
        $message = 'Your search preferences have been updated successfully!';

        // Add specific details about what was saved
        $details = [];

        if (!empty($this->form->interested_in)) {
            $details[] = 'Interests: '.implode(', ', array_map('ucfirst', $this->form->interested_in));
        }

        if (!empty($this->form->property_categories)) {
            $count = count($this->form->property_categories);
            $details[] = "Property categories: {$count} selected";
        }

        if (!empty($this->form->budget_min) || !empty($this->form->budget_max)) {
            $details[] = 'Budget range updated';
        }

        if (!empty($details)) {
            $message .= "\n\n".implode(' • ', $details);
        }

        Notification::make()
            ->success()
            ->title('Preferences Saved! 🎉')
            ->body($message)
            ->persistent()
            ->actions([
                Action::make('view_recommendations')
                    ->button()
                    ->url(route('dashboard'))
                    ->label('View Recommendations'),
            ])
            ->send();
    }

    public function getStatesProperty()
    {
        return State::orderBy('name')->get();
    }

    public function getCitiesProperty()
    {
        if (!$this->form->preferred_location_state) {
            return collect();
        }

        return City::where('state_id', $this->form->preferred_location_state)
            ->orderBy('name')
            ->get();
    }

    public function getAreasProperty()
    {
        if (!$this->form->preferred_location_city) {
            return collect();
        }

        return Area::where('city_id', $this->form->preferred_location_city)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.settings-preferences', [
            'states' => $this->states,
            'cities' => $this->cities,
            'areas' => $this->areas,
            'availablePropertyCategories' => $this->availablePropertyCategories,
        ])->layout('layouts.app');
    }
}