<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules\Password;
use App\Models\TenantInvitation;
use App\Models\User;
use App\Models\Lease;
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
        
        // Check if user with this phone already exists
        $existingUser = User::where('phone', $invitation->phone)->first();
        
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
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Check if phone is already taken by another user
        if (User::where('phone', $invitation->phone)->exists()) {
            return redirect()->route('tenant.invitation.show', $token)
                ->with('error', 'A user with this phone number already exists.');
        }
        
        DB::transaction(function () use ($request, $invitation) {
            // Create the tenant user
            $tenantUser = User::create([
                'name' => $request->name,
                'email' => $request->email ?: null, // Optional email - convert empty string to null
                'phone' => $invitation->phone, // Phone from invitation
                'password' => Hash::make($request->password),
                'user_type' => 'tenant',
                'phone_verified_at' => now(), // Auto-verify since phone was pre-validated by landlord
                'email_verified_at' => $request->email ? null : now(), // Only verify email if provided
            ]);
            
            // Create the tenant profile
            $tenantProfileData = [
                'first_name' => explode(' ', $request->name)[0] ?? $request->name,
                'last_name' => explode(' ', $request->name, 2)[1] ?? '',
                'email' => $request->email ?: null, // Optional email - convert empty string to null
                'phone' => $invitation->phone,
                'is_active' => true,
            ];
            
            // Set landlord_id and agent_id based on invitation source
            if ($invitation->agent_id) {
                // Invitation from agent - set both agent and landlord (if property has one)
                $tenantProfileData['agent_id'] = $invitation->agent_id;
                if ($invitation->landlord_id) {
                    $tenantProfileData['landlord_id'] = $invitation->landlord_id;
                }
            } else {
                // Direct invitation from landlord
                $tenantProfileData['landlord_id'] = $invitation->landlord_id;
            }
            
            $tenant = $tenantUser->tenant()->create($tenantProfileData);

            // Create initial lease and receipt for immediate access
            $this->createInitialLeaseAndReceipt($invitation, $tenant);

            // Mark invitation as accepted
            $invitation->markAsAccepted($tenantUser, $request->ip());
        });
        
        // Auto-login the new tenant
        $tenant = User::where('phone', $invitation->phone)->first();
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
                $tenantProfileData = [
                    'first_name' => explode(' ', $tenant->name)[0] ?? $tenant->name,
                    'last_name' => explode(' ', $tenant->name, 2)[1] ?? '',
                    'email' => $tenant->email ?: null,
                    'phone' => $tenant->phone,
                    'is_active' => true,
                ];
                
                // Set landlord_id and agent_id based on invitation source
                if ($invitation->agent_id) {
                    $tenantProfileData['agent_id'] = $invitation->agent_id;
                    if ($invitation->landlord_id) {
                        $tenantProfileData['landlord_id'] = $invitation->landlord_id;
                    }
                } else {
                    $tenantProfileData['landlord_id'] = $invitation->landlord_id;
                }
                
                $tenant->tenant()->create($tenantProfileData);
            } else {
                // Update associations based on invitation source
                $updateData = [];
                if ($invitation->agent_id) {
                    $updateData['agent_id'] = $invitation->agent_id;
                    if ($invitation->landlord_id) {
                        $updateData['landlord_id'] = $invitation->landlord_id;
                    }
                } else {
                    $updateData['landlord_id'] = $invitation->landlord_id;
                }
                
                $tenant->tenant()->update($updateData);
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
        
        $user = User::where('phone', $invitation->phone)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }
        
        // Check if user is a tenant
        if ($user->user_type !== 'tenant') {
            return back()->withErrors([
                'phone' => 'This account is not set up as a tenant account.',
            ]);
        }
        
        // Associate with landlord and mark invitation as accepted
        DB::transaction(function () use ($invitation, $user) {
            // Create tenant profile if it doesn't exist
            if (!$user->tenant) {
                $tenantProfileData = [
                    'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                    'last_name' => explode(' ', $user->name, 2)[1] ?? '',
                    'email' => $user->email ?: null,
                    'phone' => $user->phone,
                    'is_active' => true,
                ];
                
                // Set landlord_id and agent_id based on invitation source
                if ($invitation->agent_id) {
                    $tenantProfileData['agent_id'] = $invitation->agent_id;
                    if ($invitation->landlord_id) {
                        $tenantProfileData['landlord_id'] = $invitation->landlord_id;
                    }
                } else {
                    $tenantProfileData['landlord_id'] = $invitation->landlord_id;
                }
                
                $user->tenant()->create($tenantProfileData);
            } else {
                // Update associations based on invitation source
                $updateData = [];
                if ($invitation->agent_id) {
                    $updateData['agent_id'] = $invitation->agent_id;
                    if ($invitation->landlord_id) {
                        $updateData['landlord_id'] = $invitation->landlord_id;
                    }
                } else {
                    $updateData['landlord_id'] = $invitation->landlord_id;
                }
                
                $user->tenant()->update($updateData);
            }
            
            $invitation->markAsAccepted($user, request()->ip());
        });
        
        Auth::login($user);
        
        return redirect()->route('filament.tenant.pages.dashboard')
            ->with('success', 'Welcome back! You have been associated with your landlord.');
    }

    /**
     * Create initial lease and receipt for new tenant
     */
    private function createInitialLeaseAndReceipt(TenantInvitation $invitation, $tenant): void
    {
        // Only create if the invitation has a property
        if (!$invitation->property_id) {
            return;
        }

        $property = $invitation->property;
        $startDate = now();
        $endDate = $startDate->copy()->addYear();

        // Create lease with draft status first, then activate to trigger observer
        $lease = Lease::create([
            'property_id' => $invitation->property_id,
            'tenant_id' => $tenant->id,
            'landlord_id' => $invitation->landlord_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'yearly_rent' => $property->price ?? 600000, // Annual rent amount
            'lease_type' => Lease::TYPE_FIXED_TERM,
            'payment_frequency' => Lease::FREQUENCY_ANNUALLY,
            'status' => Lease::STATUS_DRAFT,
            'signed_date' => $startDate,
        ]);

        // Activate lease to trigger LeaseObserver for RentPayment creation
        $lease->update(['status' => Lease::STATUS_ACTIVE]);
    }
}