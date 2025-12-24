<?php

namespace App\Filament\Landlord\Resources\PropertyResource\Pages;

use Exception;
use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Models\State;
use App\Models\City;
use App\Filament\Landlord\Resources\PropertyResource;
use App\Models\PropertyOwner;
use App\Models\Property;
use App\Services\ListingCreditService;
use App\Filament\Concerns\RedirectsToPricingOnCreditError;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateProperty extends CreateRecord
{
    use RedirectsToPricingOnCreditError;
    protected static string $resource = PropertyResource::class;

    /**
     * Handle the creation of a new property record with proper validation and defaults
     * 
     * LANDLORD PROPERTY APPROACH:
     * - Landlords create properties they own directly
     * - During registration, a PropertyOwner record is created for each landlord
     * - Properties are linked to PropertyOwner records (not directly to User records)
     * - This maintains separation between user authentication and property ownership
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Get current user
        $user = Auth::user();
        
        if (!$user) {
            throw new Exception('Cannot create property: User not authenticated.');
        }

        // Find the PropertyOwner record for this user
        $propertyOwner = PropertyOwner::where('user_id', $user->id)->first();
        
        if (!$propertyOwner) {
            throw new Exception('Cannot create property: No PropertyOwner record found for this user. Please contact support.');
        }

        // No agency or agent assignment needed for landlord context
        $data['agency_id'] = null;
        $data['agent_id'] = null;

        // Set the PropertyOwner ID as the property owner (not User ID)
        $data['owner_id'] = $propertyOwner->id;

        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }

        // Set default values for required fields
        $data = $this->setDefaultValues($data);

        // Validate required relationships exist
        $this->validateRequiredRelationships($data);

        $shouldPublish = array_key_exists('is_published', $data) ? (bool) $data['is_published'] : true;
        try {
            if ($shouldPublish) {
                ListingCreditService::assertHasListingCredits($user);
            }
            if (!empty($data['is_featured'])) {
                ListingCreditService::assertHasFeaturedCredits($user);
            }
        } catch (ValidationException $exception) {
            $this->redirectToPricingForCredits($exception);
        }

        // Create the property
        $property = static::getModel()::create($data);

        if ($shouldPublish) {
            ListingCreditService::consumeListingCredits($user, $property);
        }
        if ($property->is_featured) {
            ListingCreditService::consumeFeaturedCredits($user, $property);
        }

        // Handle feature relationships
        if (isset($data['features'])) {
            $property->features()->sync($data['features']);
        }

        // Send success notification
        Notification::make()
            ->title('Property created successfully')
            ->success()
            ->body("Property '{$property->title}' has been created and added to your portfolio.")
            ->send();

        return $property;
    }

    /**
     * Generate a unique slug for the property
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (static::getModel()::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Set default values for fields that need them
     */
    private function setDefaultValues(array $data): array
    {
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'available';
        }

        // Set default published status
        if (!isset($data['is_published'])) {
            $data['is_published'] = true;
        }
        
        // Set published_at timestamp if property is being published
        if ($data['is_published'] && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Set default verification status (false for new properties)
        if (!isset($data['is_verified'])) {
            $data['is_verified'] = false;
        }

        // Set default featured status
        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        // Set published status and timestamp
        if (!isset($data['is_published'])) {
            $data['is_published'] = true;
        }
        
        // Set published_at timestamp if property is being published
        if ($data['is_published'] && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Initialize counters
        $data['view_count'] = 0;
        $data['inquiry_count'] = 0;
        $data['favorite_count'] = 0;

        return Property::applyListingPackageData($data);
    }

    /**
     * Validate that all required relationships exist
     */
    private function validateRequiredRelationships(array $data): void
    {
        // Validate property type exists
        if (!isset($data['property_type_id']) || !PropertyType::find($data['property_type_id'])) {
            throw new Exception('Invalid property type selected.');
        }

        // Validate property subtype exists
        if (!isset($data['property_subtype_id']) || !PropertySubtype::find($data['property_subtype_id'])) {
            throw new Exception('Invalid property subtype selected.');
        }

        // Validate location exists
        if (!isset($data['state_id']) || !State::find($data['state_id'])) {
            throw new Exception('Invalid state selected.');
        }

        if (!isset($data['city_id']) || !City::find($data['city_id'])) {
            throw new Exception('Invalid city selected.');
        }

        // Note: owner_id is automatically handled in handleRecordCreation method
        // It uses the PropertyOwner record associated with the current authenticated user
        // This maintains proper ownership tracking through the PropertyOwner model
    }

    /**
     * Legacy method kept for backward compatibility
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // This method is called before handleRecordCreation
        // We'll do minimal processing here and let handleRecordCreation handle the main logic
        return $data;
    }

    /**
     * Redirect after creating the property
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
