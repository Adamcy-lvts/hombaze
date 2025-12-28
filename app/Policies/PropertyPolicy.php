<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Property;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if (
            $user->user_type === 'admin'
            || $user->user_type === 'super_admin'
            || $user->hasRole(['admin', 'super_admin'])
        ) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('view_any_property');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('view_property');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('create_property');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('update_property');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('delete_property');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('delete_any_property');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Property $property): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('force_delete_property');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('force_delete_any_property');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Property $property): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('restore_property');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('restore_any_property');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Property $property): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('replicate_property');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->hasRole(['landlord', 'property_owner', 'agent', 'independent_agent'])
            || $user->can('reorder_property');
    }
}
