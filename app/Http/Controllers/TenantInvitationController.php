<?php

namespace App\Http\Controllers;

use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class TenantInvitationController extends Controller
{
    /**
     * Show the invitation acceptance form
     */
    public function show($token)
    {
        $invitation = TenantInvitation::where('token', $token)->firstOrFail();
        
        // Check if invitation is valid
        if (!$invitation->isValidForAcceptance()) {
            if ($invitation->isExpired()) {
                return view('invitation.expired', compact('invitation'));
            }
            
            if ($invitation->isAccepted()) {
                return view('invitation.already-accepted', compact('invitation'));
            }
            
            if ($invitation->isCancelled()) {
                return view('invitation.cancelled', compact('invitation'));
            }
        }
        
        // Check if user with this email already exists
        $existingUser = User::where('email', $invitation->email)->first();
        
        if ($existingUser) {
            // If user exists and is already a tenant, just associate with landlord
            if ($existingUser->user_type === 'tenant') {
                return $this->associateExistingTenant($invitation, $existingUser);
            }
            
            // If user exists but is not a tenant, show error
            return view('invitation.user-exists', compact('invitation', 'existingUser'));
        }
        
        return view('invitation.register', compact('invitation'));
    }
    
    /**
     * Process tenant registration from invitation
     */
    public function register(Request $request, $token)
    {
        $invitation = TenantInvitation::where('token', $token)->firstOrFail();
        
        // Validate invitation
        if (!$invitation->isValidForAcceptance()) {
            return redirect()->route('tenant.invitation.show', $token)
                ->with('error', 'This invitation is no longer valid.');
        }
        
        // Validate registration data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        // Check if email is already taken
        if (User::where('email', $invitation->email)->exists()) {
            return redirect()->route('tenant.invitation.show', $token)
                ->with('error', 'A user with this email already exists.');
        }
        
        DB::transaction(function () use ($request, $invitation) {
            // Create the tenant user
            $tenantUser = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'user_type' => 'tenant',
                'email_verified_at' => now(), // Auto-verify since email was pre-validated by landlord
            ]);
            
            // Create the tenant profile
            $tenantUser->tenant()->create([
                'first_name' => explode(' ', $request->name)[0] ?? $request->name,
                'last_name' => explode(' ', $request->name, 2)[1] ?? '',
                'email' => $invitation->email,
                'phone' => $request->phone,
                'landlord_id' => $invitation->landlord_id,
                'is_active' => true,
            ]);
            
            // Mark invitation as accepted
            $invitation->markAsAccepted($tenantUser, $request->ip());
        });
        
        // Auto-login the new tenant
        $tenant = User::where('email', $invitation->email)->first();
        Auth::login($tenant);
        
        return redirect()->route('filament.tenant.pages.dashboard')
            ->with('success', 'Welcome! Your tenant account has been created successfully.');
    }
    
    /**
     * Associate existing tenant with landlord
     */
    private function associateExistingTenant(TenantInvitation $invitation, User $tenant)
    {
        DB::transaction(function () use ($invitation, $tenant) {
            // Create tenant profile if it doesn't exist
            if (!$tenant->tenant) {
                $tenant->tenant()->create([
                    'first_name' => explode(' ', $tenant->name)[0] ?? $tenant->name,
                    'last_name' => explode(' ', $tenant->name, 2)[1] ?? '',
                    'email' => $tenant->email,
                    'phone' => $tenant->phone,
                    'landlord_id' => $invitation->landlord_id,
                    'is_active' => true,
                ]);
            } else {
                // Update landlord association if needed
                $tenant->tenant()->update([
                    'landlord_id' => $invitation->landlord_id,
                ]);
            }
            
            // Mark invitation as accepted
            $invitation->markAsAccepted($tenant, request()->ip());
        });
        
        return view('invitation.existing-tenant-associated', compact('invitation', 'tenant'));
    }
    
    /**
     * Handle login for existing users
     */
    public function login(Request $request, $token)
    {
        $invitation = TenantInvitation::where('token', $token)->firstOrFail();
        
        // Validate invitation
        if (!$invitation->isValidForAcceptance()) {
            return redirect()->route('tenant.invitation.show', $token)
                ->with('error', 'This invitation is no longer valid.');
        }
        
        $request->validate([
            'password' => ['required'],
        ]);
        
        $user = User::where('email', $invitation->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }
        
        // Check if user is a tenant
        if ($user->user_type !== 'tenant') {
            return back()->withErrors([
                'email' => 'This account is not set up as a tenant account.',
            ]);
        }
        
        // Associate with landlord and mark invitation as accepted
        DB::transaction(function () use ($invitation, $user) {
            // Create tenant profile if it doesn't exist
            if (!$user->tenant) {
                $user->tenant()->create([
                    'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                    'last_name' => explode(' ', $user->name, 2)[1] ?? '',
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'landlord_id' => $invitation->landlord_id,
                    'is_active' => true,
                ]);
            } else {
                // Update landlord association if needed
                $user->tenant()->update([
                    'landlord_id' => $invitation->landlord_id,
                ]);
            }
            
            $invitation->markAsAccepted($user, request()->ip());
        });
        
        Auth::login($user);
        
        return redirect()->route('filament.tenant.pages.dashboard')
            ->with('success', 'Welcome back! You have been associated with your landlord.');
    }
}