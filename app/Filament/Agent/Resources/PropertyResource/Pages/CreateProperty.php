<?php

namespace App\Filament\Agent\Resources\PropertyResource\Pages;

use App\Filament\Agent\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    /**
     * Handle the creation of a new property record with proper validation and defaults
     * 
     * INDEPENDENT AGENT PROPERTY APPROACH:
     * - Independent agents create properties without agency association
     * - Property owners are stored in separate PropertyOwner model (not users table)
     * - Property owners can be:
     *   1. Individuals who own properties
     *   2. Companies/Organizations that own properties
     *   3. Trusts, estates, or government entities
     * - Property owners do NOT need user accounts - they're simply ownership records
     * - Independent agents manage properties on behalf of property owners
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Get current user and their agent profile
        $user = auth()->user();
        Log::info('=== Property Creation Started ===', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_type' => $user->user_type,
            'form_data_keys' => array_keys($data),
        ]);
        
        $agentProfile = $user->agentProfile;
        Log::info('Agent Profile Check', [
            'agent_profile_exists' => $agentProfile ? true : false,
            'agent_profile_id' => $agentProfile ? $agentProfile->id : null,
            'agent_profile_user_id' => $agentProfile ? $agentProfile->user_id : null,
        ]);
        
        if (!$agentProfile) {
            Log::error('Agent profile not found for user', ['user_id' => $user->id]);
            throw new \Exception('Cannot create property: Current user does not have an associated agent profile.');
        }

        // Set agent assignment - this is the key difference from agency properties
        $data['agent_id'] = $agentProfile->id;
        $data['agency_id'] = null; // Independent agents have no agency
        
        Log::info('Agent and Agency Assignment', [
            'agent_id_set_to' => $data['agent_id'],
            'agency_id_set_to' => $data['agency_id'],
        ]);

        // Handle owner_id requirement - this is mandatory
        if (!isset($data['owner_id']) || empty($data['owner_id'])) {
            Log::error('Property owner not specified', ['data_keys' => array_keys($data)]);
            throw new \Exception('Property owner must be specified. Please select a property owner from the list or create a new owner profile.');
        }
        
        Log::info('Owner ID Check', ['owner_id' => $data['owner_id']]);

        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            Log::info('Generated slug', ['title' => $data['title'], 'slug' => $data['slug']]);
        }

        // Set default values for required fields
        $data = $this->setDefaultValues($data);
        Log::info('Default values set', ['status' => $data['status'], 'is_published' => $data['is_published']]);

        // Validate required relationships exist
        $this->validateRequiredRelationships($data);
        Log::info('Validation passed');

        // Log final data before creation
        Log::info('Final data before property creation', [
            'title' => $data['title'] ?? 'no title',
            'agent_id' => $data['agent_id'],
            'agency_id' => $data['agency_id'],
            'owner_id' => $data['owner_id'],
            'status' => $data['status'],
            'slug' => $data['slug'] ?? 'no slug',
        ]);

        // Create the property
        $property = static::getModel()::create($data);
        
        Log::info('Property created successfully', [
            'property_id' => $property->id,
            'property_title' => $property->title,
            'property_agent_id' => $property->agent_id,
            'property_agency_id' => $property->agency_id,
            'property_slug' => $property->slug,
        ]);

        // Send success notification
        Notification::make()
            ->title('Property created successfully')
            ->success()
            ->body("Property '{$property->title}' has been created and assigned to you as the listing agent.")
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

        return $data;
    }

    /**
     * Validate that all required relationships exist
     */
    private function validateRequiredRelationships(array $data): void
    {
        // Validate property type exists
        if (!isset($data['property_type_id']) || !\App\Models\PropertyType::find($data['property_type_id'])) {
            throw new \Exception('Invalid property type selected.');
        }

        // Validate property subtype exists
        if (!isset($data['property_subtype_id']) || !\App\Models\PropertySubtype::find($data['property_subtype_id'])) {
            throw new \Exception('Invalid property subtype selected.');
        }

        // Validate location exists
        if (!isset($data['state_id']) || !\App\Models\State::find($data['state_id'])) {
            throw new \Exception('Invalid state selected.');
        }

        if (!isset($data['city_id']) || !\App\Models\City::find($data['city_id'])) {
            throw new \Exception('Invalid city selected.');
        }

        // Validate owner exists
        if (!isset($data['owner_id']) || !\App\Models\PropertyOwner::find($data['owner_id'])) {
            throw new \Exception('Invalid property owner selected.');
        }

        // Validate agent profile exists and belongs to current user
        if (isset($data['agent_id'])) {
            $agent = \App\Models\Agent::find($data['agent_id']);
            if (!$agent || $agent->user_id !== auth()->id()) {
                throw new \Exception('Invalid agent assignment.');
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

    /**
     * Redirect after creating the property
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
