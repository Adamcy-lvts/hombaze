<?php

namespace App\Http\Middleware;

use App\Models\Property;
use App\Models\Agent;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use App\Models\SalesAgreement;
use App\Models\SalesAgreementTemplate;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyAgencyScopes
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->hasUser()) {
            $user = auth()->user();
            $agencyId = null;

            // Determine agency ID based on user type
            if ($user->user_type === 'agency_owner') {
                // For agency owners, get the first owned agency
                $agency = $user->ownedAgencies()->first();
                if ($agency) {
                    $agencyId = $agency->id;
                }
            } elseif ($user->user_type === 'agent' && $user->agentProfile && $user->agentProfile->agency_id) {
                // For agents, get agency from their profile
                $agencyId = $user->agentProfile->agency_id;
            }

            if ($agencyId) {
                // Apply global scope to Property model for agency context
                Property::addGlobalScope('agency', function (Builder $query) use ($agencyId) {
                    $query->where('agency_id', $agencyId);
                });

                // Apply global scope to Agent model for agency context
                Agent::addGlobalScope('agency', function (Builder $query) use ($agencyId) {
                    $query->where('agency_id', $agencyId);
                });

                // Apply global scope to PropertyInquiry model for agency context
                PropertyInquiry::addGlobalScope('agency', function (Builder $query) use ($agencyId) {
                    $query->whereHas('property', function (Builder $subQuery) use ($agencyId) {
                        $subQuery->where('agency_id', $agencyId);
                    });
                });

                // Apply global scope to PropertyViewing model for agency context
                PropertyViewing::addGlobalScope('agency', function (Builder $query) use ($agencyId) {
                    $query->whereHas('property', function (Builder $subQuery) use ($agencyId) {
                        $subQuery->where('agency_id', $agencyId);
                    });
                });

                // Apply global scope to SalesAgreement model for agency context
                SalesAgreement::addGlobalScope('agency', function (Builder $query) use ($agencyId) {
                    $query->where('agency_id', $agencyId);
                });

                // Apply global scope to SalesAgreementTemplate model for agency context
                SalesAgreementTemplate::addGlobalScope('agency', function (Builder $query) use ($agencyId) {
                    $query->where('agency_id', $agencyId);
                });
            }
        }

        return $next($request);
    }
}
