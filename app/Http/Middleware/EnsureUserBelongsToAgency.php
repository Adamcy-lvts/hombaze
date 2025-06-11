<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToAgency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->hasUser()) {
            return redirect()->route('filament.agency.auth.login');
        }

        $user = auth()->user();

        // Check if user has agency_owner or agent user type
        if (!in_array($user->user_type, ['agency_owner', 'agent'])) {
            abort(403, 'Access denied. You must be an agency owner or agent to access this panel.');
        }

        // For agency agents, ensure they have an agent record with agency_id
        if ($user->user_type === 'agent') {
            if (!$user->agentProfile || !$user->agentProfile->agency_id) {
                abort(403, 'Access denied. You must be assigned to an agency to access this panel.');
            }
        }

        // For agency owners, ensure they own an agency or have agency access
        if ($user->user_type === 'agency_owner') {
            if (!$user->ownedAgencies->count() && !$user->agencies->count()) {
                abort(403, 'Access denied. You must own or belong to an agency to access this panel.');
            }
        }

        return $next($request);
    }
}
