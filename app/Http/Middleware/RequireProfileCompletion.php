<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for guests
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Skip profile completion check for these routes
        $skipRoutes = [
            'filament.*.pages.profile-completion',
            'logout',
            'filament.*.auth.logout',
        ];

        foreach ($skipRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        // Check if user requires profile completion
        if ($user->requiresProfileCompletion()) {
            // Redirect to appropriate profile completion wizard
            $completionRoute = match($user->user_type) {
                'agent' => 'filament.agent.pages.profile-completion',
                'property_owner' => 'filament.landlord.pages.profile-completion',
                'agency_owner' => 'filament.agency.pages.profile-completion',
                'tenant' => 'filament.tenant.pages.profile-completion',
                default => null,
            };

            if ($completionRoute && !$request->routeIs($completionRoute)) {
                return redirect()->route($completionRoute);
            }
        }

        return $next($request);
    }
}
