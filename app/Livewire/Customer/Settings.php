<?php

namespace App\Livewire\Customer;

use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertyType;
use App\Models\CustomerProfile;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Settings extends Component
{
    // Profile Information
    public $name;
    public $email;
    public $phone;
    public $address;
    public $bio;

    // Search Preferences
    public $preferred_location_state;
    public $preferred_location_city;
    public $preferred_location_area;
    public $preferred_property_types = [];
    public $preferred_listing_type;
    public $min_budget;
    public $max_budget;

    // Notification Settings
    public $email_notifications = true;
    public $new_properties = true;
    public $price_alerts = true;
    public $inquiry_responses = true;
    public $marketing_emails = false;

    // Privacy & Security
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = auth()->user();

        // Load profile data
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->bio = $user->bio;

        // Load search preferences from customer profile if exists
        if ($user->customerProfile) {
            $profile = $user->customerProfile;

            // Load location preferences
            $locations = $profile->preferred_locations ?? [];
            $this->preferred_location_state = $locations['state'] ?? null;
            $this->preferred_location_city = $locations['city'] ?? null;
            $this->preferred_location_area = $locations['area'] ?? null;

            // Load property types
            $propertyTypes = $profile->preferred_property_types;
            if (is_string($propertyTypes)) {
                $this->preferred_property_types = json_decode($propertyTypes, true) ?? [];
            } elseif (is_array($propertyTypes)) {
                $this->preferred_property_types = array_map('strval', $propertyTypes);
            }

            // Load listing type from interested_in
            $interests = $profile->interested_in ?? [];
            if (in_array('renting', $interests)) {
                $this->preferred_listing_type = 'rent';
            } elseif (in_array('buying', $interests)) {
                $this->preferred_listing_type = 'sale';
            }

            // Load budget
            $this->min_budget = $profile->budget_min;
            $this->max_budget = $profile->budget_max;
        }

        // Load notification preferences
        $this->email_notifications = $user->email_notifications ?? true;
        $this->new_properties = $user->new_properties ?? true;
        $this->price_alerts = $user->price_alerts ?? true;
        $this->inquiry_responses = $user->inquiry_responses ?? true;
        $this->marketing_emails = $user->marketing_emails ?? false;
    }

    public function updatedPreferredLocationState()
    {
        $this->preferred_location_city = null;
        $this->preferred_location_area = null;
    }

    public function updatedPreferredLocationCity()
    {
        $this->preferred_location_area = null;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'bio' => $this->bio,
        ]);

        session()->flash('success', 'Profile updated successfully!');
    }

    public function updateSearchPreferences()
    {
        $this->validate([
            'preferred_location_state' => 'nullable|exists:states,id',
            'preferred_location_city' => 'nullable|exists:cities,id',
            'preferred_location_area' => 'nullable|exists:areas,id',
            'preferred_property_types' => 'array',
            'preferred_property_types.*' => 'integer|min:1',
            'preferred_listing_type' => 'nullable|in:rent,sale',
            'min_budget' => 'nullable|numeric|min:0',
            'max_budget' => 'nullable|numeric|min:0|gte:min_budget',
        ]);

        $user = auth()->user();

        // Prepare location preferences array
        $preferredLocations = [];
        if ($this->preferred_location_state) {
            $preferredLocations['state'] = $this->preferred_location_state;
        }
        if ($this->preferred_location_city) {
            $preferredLocations['city'] = $this->preferred_location_city;
        }
        if ($this->preferred_location_area) {
            $preferredLocations['area'] = $this->preferred_location_area;
        }

        // Prepare interested_in array based on listing type
        $interestedIn = [];
        if ($this->preferred_listing_type === 'rent') {
            $interestedIn[] = 'renting';
        } elseif ($this->preferred_listing_type === 'sale') {
            $interestedIn[] = 'buying';
        }

        // Create or update customer profile
        $user->customerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_locations' => !empty($preferredLocations) ? $preferredLocations : null,
                'preferred_property_types' => !empty($this->preferred_property_types) ? array_map('intval', $this->preferred_property_types) : null,
                'interested_in' => !empty($interestedIn) ? $interestedIn : null,
                'budget_min' => $this->min_budget ? (float) $this->min_budget : null,
                'budget_max' => $this->max_budget ? (float) $this->max_budget : null,
            ]
        );

        session()->flash('success', 'Search preferences updated successfully!');
    }

    public function updateNotifications()
    {
        auth()->user()->update([
            'email_notifications' => $this->email_notifications,
            'new_properties' => $this->new_properties,
            'price_alerts' => $this->price_alerts,
            'inquiry_responses' => $this->inquiry_responses,
            'marketing_emails' => $this->marketing_emails,
        ]);

        session()->flash('success', 'Notification preferences updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('success', 'Password updated successfully!');
    }

    public function getCitiesProperty()
    {
        if (!$this->preferred_location_state) {
            return collect();
        }

        return City::where('state_id', $this->preferred_location_state)
            ->orderBy('name')
            ->get();
    }

    public function getAreasProperty()
    {
        if (!$this->preferred_location_city) {
            return collect();
        }

        return Area::where('city_id', $this->preferred_location_city)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.settings', [
            'states' => State::orderBy('name')->get(),
            'cities' => $this->cities,
            'areas' => $this->areas,
            'propertyTypes' => PropertyType::orderBy('name')->get(),
        ])->layout('layouts.landing');
    }
}