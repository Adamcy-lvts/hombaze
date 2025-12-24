<?php

namespace App\Filament\Agency\Resources\PropertyResource\Pages;

use Exception;
use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Models\State;
use App\Models\City;
use App\Models\PropertyOwner;
use App\Models\Agent;
use App\Filament\Agency\Resources\PropertyResource;
use App\Models\Property;
use App\Services\ListingCreditService;
use App\Filament\Concerns\RedirectsToPricingOnCreditError;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreateProperty extends CreateRecord
{
    use RedirectsToPricingOnCreditError;
    protected static string $resource = PropertyResource::class;

    /**
     * Handle the creation of a new property record with proper validation and defaults
     * 
     * PROPERTY OWNER APPROACH:
     * - Property owners are stored in separate PropertyOwner model (not users table)
     * - Property owners can be:
     *   1. Individuals who own properties
     *   2. Companies/Organizations that own properties
     *   3. Trusts, estates, or government entities
     * - Property owners do NOT need user accounts - they're simply ownership records
     * - Agents/Agencies manage properties on behalf of property owners
     * - If property owners later want platform access, they can create separate user accounts
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Get current agency and user
        $agency = Filament::getTenant();
        $user = auth()->user();
        
        if (!$agency) {
            throw new Exception('Cannot create property: No agency context found.');
        }

        // Set required agency relationship
        $data['agency_id'] = $agency->id;

        // Handle owner_id requirement - this is mandatory
        if (!isset($data['owner_id']) || empty($data['owner_id'])) {
            // If no owner is specified, check if current user can be the owner
            // In agency context, properties are typically managed for clients
            // So we'll require this to be explicitly set via the form
            if (!isset($data['owner_id'])) {
                throw new Exception('Property owner must be specified. Please select a property owner from the list or create a new owner profile.');
            }
        }

        // Handle agent assignment with smart defaults
        if (!isset($data['agent_id']) || empty($data['agent_id'])) {
            // If current user is an agent in this agency, assign to them
            $agent = $user->agentProfile()->where('agency_id', $agency->id)->first();
            if ($agent) {
                $data['agent_id'] = $agent->id;
            }
        }

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
                ListingCreditService::assertHasListingCredits($agency);
            }
            if (!empty($data['is_featured'])) {
                ListingCreditService::assertHasFeaturedCredits($agency);
            }
        } catch (ValidationException $exception) {
            $this->redirectToPricingForCredits($exception);
        }

        // Create the property
        $property = static::getModel()::create($data);

        if ($shouldPublish) {
            ListingCreditService::consumeListingCredits($agency, $property);
        }
        if ($property->is_featured) {
            ListingCreditService::consumeFeaturedCredits($agency, $property);
        }

        // Send success notification
        Notification::make()
            ->title('Property created successfully')
            ->success()
            ->body("Property '{$property->title}' has been created and assigned to the agency.")
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

        // Validate owner exists
        if (!isset($data['owner_id']) || !PropertyOwner::find($data['owner_id'])) {
            throw new Exception('Invalid property owner selected.');
        }

        // Validate agent if provided
        if (isset($data['agent_id']) && !empty($data['agent_id'])) {
            $agent = Agent::find($data['agent_id']);
            if (!$agent || $agent->agency_id !== $data['agency_id']) {
                throw new Exception('Invalid agent selected or agent does not belong to this agency.');
            }
        }
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
}
