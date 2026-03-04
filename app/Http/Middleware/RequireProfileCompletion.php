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

        if ($user->requiresProfileCompletion()) {
            $completionRoute = null;
            $parameters = [];

            switch ($user->user_type) {
                case 'agent':
                    $completionRoute = 'filament.agent.pages.profile-completion';
                    break;
                case 'property_owner':
                    $completionRoute = 'filament.property-owner.pages.profile-completion';
                    break;
                case 'agency_owner':
                    $completionRoute = 'filament.agency.pages.profile-completion';
                    // Agency panel is multi-tenant, so we must provide the tenant parameter
                    $agency = $user->ownedAgencies()->first();
                    if ($agency) {
                        $parameters = ['tenant' => $agency];
                    }
                    break;
                case 'tenant':
                    $completionRoute = 'filament.tenant.pages.profile-completion';
                    break;
            }

            if ($completionRoute && !$request->routeIs($completionRoute)) {
                return redirect()->route($completionRoute, $parameters);
            }
        }

        return $next($request);
    }
}
