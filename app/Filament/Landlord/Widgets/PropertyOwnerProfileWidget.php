<?php

namespace App\Filament\Landlord\Widgets;

use App\Models\PropertyOwner;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PropertyOwnerProfileWidget extends Widget
{
    protected string $view = 'filament.landlord.widgets.property-owner-profile-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        $user = Auth::user();
        $propertyOwner = PropertyOwner::where('user_id', $user->id)->first();

        if (!$propertyOwner) {
            return [
                'hasProfile' => false,
                'completionPercentage' => 0,
                'missingFields' => [],
                'propertyOwner' => null,
            ];
        }

        // Calculate profile completion
        $requiredFields = [
            'type' => $propertyOwner->type,
            'name' => $propertyOwner->type === 'company' ? $propertyOwner->company_name :
                     ($propertyOwner->first_name && $propertyOwner->last_name),
            'email' => $propertyOwner->email,
            'phone' => $propertyOwner->phone,
            'address' => $propertyOwner->address,
            'state_id' => $propertyOwner->state_id,
            'city_id' => $propertyOwner->city_id,
        ];

        $optionalFields = [
            'area_id' => $propertyOwner->area_id,
            'tax_id' => $propertyOwner->tax_id,
            'profile_photo' => $propertyOwner->profile_photo,
            'preferred_communication' => $propertyOwner->preferred_communication,
        ];

        $completedRequired = collect($requiredFields)->filter()->count();
        $totalRequired = count($requiredFields);
        $completedOptional = collect($optionalFields)->filter()->count();
        $totalOptional = count($optionalFields);

        $completionPercentage = round((($completedRequired * 0.8) + ($completedOptional * 0.2)) / ($totalRequired * 0.8 + $totalOptional * 0.2) * 100);

        $missingFields = collect($requiredFields)
            ->filter(fn($value) => !$value)
            ->keys()
            ->map(fn($key) => $this->getFieldLabel($key))
            ->toArray();

        return [
            'hasProfile' => true,
            'completionPercentage' => $completionPercentage,
            'missingFields' => $missingFields,
            'propertyOwner' => $propertyOwner,
            'isComplete' => $completionPercentage >= 80,
        ];
    }

    private function getFieldLabel(string $field): string
    {
        return match($field) {
            'type' => 'Owner Type',
            'name' => 'Full Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'address' => 'Street Address',
            'state_id' => 'State',
            'city_id' => 'City',
            'area_id' => 'Area',
            'tax_id' => 'Tax ID',
            'profile_photo' => 'Profile Photo',
            'preferred_communication' => 'Communication Preference',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }

    public static function canView(): bool
    {
        return Auth::user()->hasRole(['landlord', 'property_owner']);
    }
}