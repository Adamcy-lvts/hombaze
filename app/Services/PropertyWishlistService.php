<?php

namespace App\Services;

use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Illuminate\Support\Collection;

class PropertyWishlistService
{
    /**
     * Get all saved property IDs for a specific user.
     *
     * @param int|User $user
     * @return array
     */
    public function getSavedPropertyIds($user): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        return SavedProperty::where('user_id', $userId)
            ->pluck('property_id')
            ->toArray();
    }

    /**
     * Toggle the saved status of a property for a user.
     *
     * @param int|User $user
     * @param int|Property $property
     * @return bool True if saved, false if unsaved
     */
    public function toggleSave($user, $property): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        $propertyId = $property instanceof Property ? $property->id : $property;

        $savedProperty = SavedProperty::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->first();

        if ($savedProperty) {
            $savedProperty->delete();
            return false;
        }

        SavedProperty::create([
            'user_id' => $userId,
            'property_id' => $propertyId,
        ]);

        return true;
    }

    /**
     * Check if a property is saved by a user.
     *
     * @param int|User $user
     * @param int|Property $property
     * @return bool
     */
    public function isSaved($user, $property): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        $propertyId = $property instanceof Property ? $property->id : $property;

        return SavedProperty::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->exists();
    }
}
