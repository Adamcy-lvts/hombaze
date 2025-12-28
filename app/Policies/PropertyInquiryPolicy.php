<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PropertyInquiry;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyInquiryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('view_any_property::inquiry');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyInquiry $propertyInquiry): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('view_property::inquiry');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('create_property::inquiry');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyInquiry $propertyInquiry): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('update_property::inquiry');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyInquiry $propertyInquiry): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('delete_property::inquiry');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('delete_any_property::inquiry');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, PropertyInquiry $propertyInquiry): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('force_delete_property::inquiry');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('force_delete_any_property::inquiry');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, PropertyInquiry $propertyInquiry): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('restore_property::inquiry');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('restore_any_property::inquiry');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, PropertyInquiry $propertyInquiry): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('replicate_property::inquiry');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->hasRole(['agent', 'independent_agent'])
            || $user->can('reorder_property::inquiry');
    }
}
