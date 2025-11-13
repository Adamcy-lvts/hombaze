<?php

namespace App\Livewire\Forms;

use Exception;
use Livewire\Form;
use App\Models\CustomerProfile;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;

class CustomerPreferencesForm extends Form
{
    public ?CustomerProfile $customerProfile = null;

    // Location preferences
    public ?int $preferred_location_state = null;
    public ?int $preferred_location_city = null;
    public ?int $preferred_location_area = null;

    // Interest preferences
    #[Validate('required|array|min:1', message: 'Please select at least one interest.')]
    public array $interested_in = [];

    // Property categories
    public array $property_categories = [];

    // Property subtypes
    public array $apartment_subtypes = [];
    public array $house_subtypes = [];
    public array $land_sizes = [];
    public bool $shop_selected = false;

    // Budget preferences
    public array $budgets = [
        'house_buy' => ['min' => '', 'max' => ''],
        'house_rent' => ['min' => '', 'max' => ''],
        'land_buy' => ['min' => '', 'max' => ''],
        'shop_buy' => ['min' => '', 'max' => ''],
        'shop_rent' => ['min' => '', 'max' => ''],
    ];

    // Legacy budget fields (for backward compatibility)
    public string $budget_min = '';
    public string $budget_max = '';

    // Notification preferences
    public bool $email_alerts = true;
    public bool $sms_alerts = false;
    public bool $whatsapp_alerts = false;

    public function setCustomerProfile(CustomerProfile $profile): void
    {
        $this->customerProfile = $profile;

        // Load location preferences
        $locations = $profile->preferred_locations ?? [];
        $this->preferred_location_state = $locations['state'] ?? null;
        $this->preferred_location_city = $locations['city'] ?? null;
        $this->preferred_location_area = $locations['area'] ?? null;

        // Load interests
        $this->interested_in = $profile->interested_in ?? [];

        // Load property preferences
        $this->property_categories = $profile->property_categories ?? [];
        $this->apartment_subtypes = $profile->apartment_subtypes ?? [];
        $this->house_subtypes = $profile->house_subtypes ?? [];
        $this->land_sizes = $profile->land_sizes ?? [];
        $this->shop_selected = $profile->shop_selected ?? false;

        // Load budgets
        $this->budgets = $profile->budgets ?? [
            'house_buy' => ['min' => '', 'max' => ''],
            'house_rent' => ['min' => '', 'max' => ''],
            'land_buy' => ['min' => '', 'max' => ''],
            'shop_buy' => ['min' => '', 'max' => ''],
            'shop_rent' => ['min' => '', 'max' => ''],
        ];

        // Legacy budget fields
        $this->budget_min = $profile->budget_min ? (string) intval((float) $profile->budget_min) : '';
        $this->budget_max = $profile->budget_max ? (string) intval((float) $profile->budget_max) : '';

        // Notification preferences
        $this->email_alerts = $profile->email_alerts ?? true;
        $this->sms_alerts = $profile->sms_alerts ?? false;
        $this->whatsapp_alerts = $profile->whatsapp_alerts ?? false;
    }

    public function store(): void
    {
        $this->validate();

        $user = auth()->user();

        // Prepare location preferences
        $preferredLocations = [];
        if (!empty($this->preferred_location_state)) {
            $preferredLocations['state'] = $this->preferred_location_state;
        }
        if (!empty($this->preferred_location_city)) {
            $preferredLocations['city'] = $this->preferred_location_city;
        }
        if (!empty($this->preferred_location_area)) {
            $preferredLocations['area'] = $this->preferred_location_area;
        }

        // Update or create customer profile
        $this->customerProfile = $user->customerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_locations' => !empty($preferredLocations) ? $preferredLocations : null,
                'interested_in' => !empty($this->interested_in) ? $this->interested_in : null,
                'property_categories' => !empty($this->property_categories) ? $this->property_categories : null,
                'apartment_subtypes' => !empty($this->apartment_subtypes) ? $this->apartment_subtypes : null,
                'house_subtypes' => !empty($this->house_subtypes) ? $this->house_subtypes : null,
                'land_sizes' => !empty($this->land_sizes) ? $this->land_sizes : null,
                'shop_selected' => $this->shop_selected,
                'budgets' => !empty($this->budgets) ? $this->budgets : null,
                'budget_min' => !empty($this->budget_min) && $this->budget_min !== '' ? (float) str_replace(',', '', $this->budget_min) : null,
                'budget_max' => !empty($this->budget_max) && $this->budget_max !== '' ? (float) str_replace(',', '', $this->budget_max) : null,
                'email_alerts' => $this->email_alerts,
                'sms_alerts' => $this->sms_alerts,
                'whatsapp_alerts' => $this->whatsapp_alerts,
            ]
        );
    }

    public function autoSave(): void
    {
        if (!$this->customerProfile) {
            $user = auth()->user();
            $this->customerProfile = $user->customerProfile;
        }

        // Only auto-save if we have a customer profile
        if ($this->customerProfile) {
            try {
                $this->store();
            } catch (Exception $e) {
                Log::error('Auto-save preferences failed', ['error' => $e->getMessage()]);
            }
        }
    }

    public function getAvailablePropertyCategories(): array
    {
        $interests = $this->interested_in;
        $categories = [];

        if (empty($interests)) {
            return [];
        }

        // House categories
        if (in_array('buying', $interests) || in_array('renting', $interests)) {
            if (in_array('buying', $interests)) {
                $categories[] = [
                    'value' => 'house_buy',
                    'label' => 'Houses & Apartments (Buy)',
                    'description' => 'Residential properties for purchase',
                ];
            }
            if (in_array('renting', $interests) || in_array('shortlet', $interests)) {
                $categories[] = [
                    'value' => 'house_rent',
                    'label' => 'Houses & Apartments (Rent)',
                    'description' => 'Residential properties for rent',
                ];
            }
        }

        // Land categories
        if (in_array('buying', $interests)) {
            $categories[] = [
                'value' => 'land_buy',
                'label' => 'Land & Plots',
                'description' => 'Land and plots for purchase',
            ];
        }

        // Shop categories
        if (in_array('buying', $interests)) {
            $categories[] = [
                'value' => 'shop_buy',
                'label' => 'Commercial Shops (Buy)',
                'description' => 'Commercial spaces for purchase',
            ];
        }
        if (in_array('renting', $interests)) {
            $categories[] = [
                'value' => 'shop_rent',
                'label' => 'Commercial Shops (Rent)',
                'description' => 'Commercial spaces for rent',
            ];
        }

        return $categories;
    }
}
