<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TenantInvitation;

class RequireLandlordAssociation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Only apply to tenant users
        if (!$user || $user->user_type !== 'tenant') {
            return $next($request);
        }
        
        // Check if tenant has both an accepted invitation and a tenant profile with landlord association
        $hasAcceptedInvitation = TenantInvitation::where('tenant_user_id', $user->id)
            ->where('status', TenantInvitation::STATUS_ACCEPTED)
            ->exists();
            
        $hasTenantProfile = $user->tenant && $user->tenant->landlord_id;
        
        $hasLandlordAssociation = $hasAcceptedInvitation && $hasTenantProfile;
            
        if (!$hasLandlordAssociation) {
            // If this is an API request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tenant account requires landlord association. Please contact your landlord for an invitation.'
                ], 403);
            }
            
            // For web requests, redirect to a page explaining the requirement
            return redirect()->route('tenant.no-landlord')
                ->with('error', 'Your tenant account requires association with a landlord. Please contact your landlord for an invitation.');
        }
        
        return $next($request);
    }
}