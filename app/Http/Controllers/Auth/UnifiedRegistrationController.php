<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Agent;
use App\Models\Agency;
use App\Models\PropertyOwner;
use App\Models\CustomerProfile;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UnifiedRegistrationController extends Controller
{
    /**
     * Show the unified registration form
     */
    public function show(): View
    {
        return view('auth.unified-register', [
            'userTypes' => $this->getUserTypeOptions(),
            'states' => State::orderBy('name')->get(),
            'cities' => City::orderBy('name')->get(),
            'areas' => Area::orderBy('name')->get(),
        ]);
    }

    /**
     * Get available user types for registration (excluding tenant)
     */
    private function getUserTypeOptions(): array
    {
        return [
            'customer' => [
                'label' => 'Property Seeker',
                'description' => 'Find, save, and inquire about properties',
                'icon' => 'heroicon-o-heart',
                'panel' => null, // Uses main dashboard, not Filament panel
                'features' => ['Save Favorites', 'Contact Agents', 'Property Alerts', 'Rate & Review'],
                'popular' => true // Mark as most popular option
            ],
            'agent' => [
                'label' => 'Real Estate Agent',
                'description' => 'Independent real estate professional',
                'icon' => 'heroicon-o-user-circle',
                'panel' => 'agent',
                'features' => ['List Properties', 'Manage Clients', 'Track Commissions']
            ],
            'property_owner' => [
                'label' => 'Property Owner/Landlord',
                'description' => 'Property owner or landlord',
                'icon' => 'heroicon-o-home',
                'panel' => 'landlord',
                'features' => ['List Properties', 'Manage Tenants', 'Collect Rent']
            ],
            'agency_owner' => [
                'label' => 'Real Estate Agency',
                'description' => 'Real estate agency or brokerage',
                'icon' => 'heroicon-o-building-office',
                'panel' => 'agency',
                'features' => ['Manage Agents', 'Agency Branding', 'Team Performance']
            ],
            // Note: 'tenant' is intentionally excluded - tenants register via landlord invitations only
        ];
    }

    /**
     * Handle registration request with smart routing
     */
    public function register(Request $request): RedirectResponse
    {
        Log::info('Unified registration attempt', [
            'user_type' => $request->input('user_type'),
            'email' => $request->input('email'),
            'csrf_token' => $request->input('_token'),
            'session_id' => session()->getId(),
        ]);

        // Validate common fields
        $request->validate([
            'user_type' => 'required|in:customer,agent,property_owner,agency_owner',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',

            // No additional customer fields required during registration
        ]);

        DB::beginTransaction();
        
        try {
            // Create base user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'user_type' => $request->user_type,
                'is_active' => true,
            ]);

            Log::info('User created for unified registration', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ]);

            // Create user type specific records and assign roles
            $this->createUserTypeSpecificData($user, $request->all());

            // Initialize profile completion tracking
            $user->initializeProfileCompletion();

            // Fire registered event
            event(new Registered($user));

            DB::commit();

            // Login the user
            Auth::login($user);

            // Redirect to appropriate panel
            return $this->redirectToPanel($user);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Unified registration failed', [
                'email' => $request->email,
                'user_type' => $request->user_type,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['registration' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Create user type specific data and assign roles
     */
    private function createUserTypeSpecificData(User $user, array $data): void
    {
        switch ($user->user_type) {
            case 'customer':
                $this->createCustomerProfile($user, $data);
                // Customers don't need specific roles initially
                break;

            case 'agent':
                $this->createAgentProfile($user, $data);
                $this->assignRole($user, 'independent_agent');
                break;

            case 'property_owner':
                $this->createPropertyOwnerProfile($user, $data);
                $this->assignRole($user, 'landlord');
                break;

            case 'agency_owner':
                $this->createAgencyForOwner($user, $data);
                $this->assignRole($user, 'agency_owner');
                break;
        }
    }

    /**
     * Create basic agent profile
     */
    private function createAgentProfile(User $user, array $data): void
    {
        Agent::create([
            'user_id' => $user->id,
            'bio' => 'New agent on HomeBaze platform',
            'years_experience' => 0,
            'specializations' => 'residential_sales,residential_rentals',
            'service_areas' => json_encode([]),
            'languages' => json_encode(['english']),
            'is_available' => true,
            'is_verified' => false,
            'accepts_new_clients' => true,
        ]);

        Log::info('Agent profile created', ['user_id' => $user->id]);
    }

    /**
     * Create basic property owner profile
     */
    private function createPropertyOwnerProfile(User $user, array $data): void
    {
        $nameParts = explode(' ', $user->name, 2);
        
        PropertyOwner::create([
            'user_id' => $user->id,
            'type' => 'individual',
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'email' => $user->email,
            'phone' => $user->phone,
        ]);

        Log::info('PropertyOwner profile created', ['user_id' => $user->id]);
    }

    /**
     * Create basic customer profile
     */
    private function createCustomerProfile(User $user, array $data): void
    {
        // Create minimal customer profile - preferences will be set later
        CustomerProfile::create([
            'user_id' => $user->id,
            'interested_in' => ['renting'], // Default minimal interest
            'budget_min' => null,
            'budget_max' => null,
            'preferred_locations' => [],
            'email_alerts' => true, // Default to enabled
            'sms_alerts' => false,
            'whatsapp_alerts' => false,
            'notification_preferences' => [
                'new_properties' => true,
                'price_drops' => true,
                'agent_responses' => true,
                'property_updates' => false,
            ],
        ]);

        Log::info('Minimal customer profile created', ['user_id' => $user->id]);
    }

    /**
     * Assign role to user
     */
    private function assignRole(User $user, string $roleName): void
    {
        try {
            $role = Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();
            
            if ($role) {
                $user->assignRole($role);
                Log::info("Assigned {$roleName} role to user", ['user_id' => $user->id]);
            } else {
                Log::warning("Role {$roleName} not found", ['user_id' => $user->id]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to assign role {$roleName}", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect to appropriate panel after registration
     */
    private function redirectToPanel(User $user): RedirectResponse
    {
        return match($user->user_type) {
            'customer' => redirect()->route('dashboard')->with('success', 'Welcome to HomeBaze! Let\'s complete your profile to find perfect properties.'),
            'agent' => $this->redirectAgentToDashboard($user),
            'property_owner' => redirect()->route('filament.landlord.pages.dashboard'),
            'agency_owner' => $this->redirectToAgencyDashboard($user),
            default => redirect()->route('dashboard')
        };
    }

    /**
     * Route agency owners to their agency dashboard
     */
    private function redirectToAgencyDashboard(User $user): RedirectResponse
    {
        // Get the user's first agency as tenant
        $agency = $user->ownedAgencies()->first() ?? $user->agencies()->first();

        if ($agency) {
            return redirect()->route('filament.agency.pages.agency-dashboard', ['tenant' => $agency]);
        }

        // If no agency found, redirect to agency registration
        return redirect()->route('filament.agency.auth.register')->with('info', 'Please complete your agency registration to access the agency dashboard.');
    }

    /**
     * Route agents to appropriate dashboard (agency or independent)
     */
    private function redirectAgentToDashboard(User $user): RedirectResponse
    {
        // Check if agent belongs to any agency
        $agency = $user->agencies()->first();

        if ($agency) {
            // Redirect to agency dashboard with tenant context
            return redirect()->route('filament.agency.pages.agency-dashboard', ['tenant' => $agency]);
        }

        // Independent agent - redirect to agent panel
        return redirect()->route('filament.agent.pages.dashboard');
    }

    /**
     * Create agency for agency owner with minimal required data
     */
    private function createAgencyForOwner(User $user, array $data): void
    {
        // Create basic agency with minimal data
        $agency = Agency::create([
            'name' => $user->name . "'s Agency",
            'slug' => Str::slug($user->name . '-agency'),
            'description' => 'Real estate agency managed by ' . $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'owner_id' => $user->id,
            'address' => [
                'street' => '',
                'city_id' => null,
                'state_id' => null,
                'area_id' => null,
            ],
            'social_media' => [],
            'specializations' => 'residential_sales,residential_rentals',
            'years_in_business' => 0,
            'rating' => 0.0,
            'total_reviews' => 0,
            'total_properties' => 0,
            'total_agents' => 1,
            'is_verified' => false,
            'is_featured' => false,
            'is_active' => true,
        ]);

        // Associate user with agency as owner
        $agency->users()->attach($user->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Create agent profile for the agency owner
        $this->createAgentProfileForOwner($user, $agency);

        Log::info('Agency created for owner via unified registration', [
            'user_id' => $user->id,
            'agency_id' => $agency->id,
            'agency_name' => $agency->name,
        ]);
    }

    /**
     * Create agent profile for agency owner
     */
    private function createAgentProfileForOwner(User $user, Agency $agency): void
    {
        $nameParts = explode(' ', $user->name, 2);

        Agent::create([
            'user_id' => $user->id,
            'agency_id' => $agency->id,
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'email' => $user->email,
            'phone' => $user->phone,
            'bio' => "Agency owner and super admin for {$agency->name}.",
            'specializations' => $agency->specializations,
            'years_experience' => 0,
            'languages' => 'English',
            'is_verified' => true,
            'is_featured' => true,
            'is_active' => true,
            'accepts_new_clients' => true,
            'state_id' => null,
            'city_id' => null,
            'area_id' => null,
        ]);

        Log::info('Agent profile created for agency owner', [
            'user_id' => $user->id,
            'agency_id' => $agency->id,
        ]);
    }
}