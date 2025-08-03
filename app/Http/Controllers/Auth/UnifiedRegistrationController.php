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
use App\Models\PropertyOwner;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Spatie\Permission\Models\Role;

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
            'agent' => [
                'label' => 'Real Estate Agent',
                'description' => 'Independent real estate professional',
                'icon' => 'heroicon-o-user-circle',
                'panel' => 'agent'
            ],
            'property_owner' => [
                'label' => 'Property Owner/Landlord',
                'description' => 'Property owner or landlord',
                'icon' => 'heroicon-o-home',
                'panel' => 'landlord'
            ],
            'agency_owner' => [
                'label' => 'Real Estate Agency',
                'description' => 'Real estate agency or brokerage',
                'icon' => 'heroicon-o-building-office',
                'panel' => 'agency'
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
            'user_type' => 'required|in:agent,property_owner,agency_owner',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
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
            case 'agent':
                $this->createAgentProfile($user, $data);
                $this->assignRole($user, 'independent_agent');
                break;

            case 'property_owner':
                $this->createPropertyOwnerProfile($user, $data);
                $this->assignRole($user, 'landlord');
                break;

            case 'agency_owner':
                // For agency, we redirect to the full agency registration flow
                // This is a placeholder - the full agency registration should be handled
                // by the existing Agency Filament registration
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
            'agent' => redirect()->route('filament.agent.pages.dashboard'),
            'property_owner' => redirect()->route('filament.landlord.pages.dashboard'),
            'agency_owner' => redirect()->route('filament.agency.pages.dashboard'),
            default => redirect()->route('dashboard')
        };
    }
}