<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class UnifiedLoginController extends Controller
{
    /**
     * Show the unified login form
     */
    public function show(): View
    {
        return view('auth.unified-login');
    }

    /**
     * Handle login request with smart routing
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'credential' => 'required|string',
            'password' => 'required|string',
        ]);

        $credential = $request->input('credential');
        $password = $request->input('password');

        // Determine if credential is email or phone
        $field = $this->getCredentialField($credential);

        // Attempt authentication
        if (Auth::attempt([$field => $credential, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Update last login timestamp
            $user->update(['last_login_at' => now()]);

            // Redirect to appropriate dashboard based on user type
            return $this->redirectToDashboard($user);
        }

        throw ValidationException::withMessages([
            'credential' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Successfully logged out.');
    }

    /**
     * Determine if credential is email or phone
     */
    private function getCredentialField(string $credential): string
    {
        return filter_var($credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }

    /**
     * Smart routing to appropriate dashboard based on user type
     */
    private function redirectToDashboard(User $user): RedirectResponse
    {
        return match($user->user_type) {
            'super_admin', 'admin' => redirect()->route('filament.admin.pages.dashboard'),
            'agency_owner' => $this->redirectToAgencyDashboard($user),
            'agent' => $this->redirectAgentToDashboard($user),
            'property_owner' => redirect()->route('filament.landlord.pages.dashboard'),
            'tenant' => redirect()->route('filament.tenant.pages.dashboard'),
            default => redirect()->route('filament.tenant.pages.dashboard') // Default fallback
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
        
        // If no agency found, redirect to agent panel
        return redirect()->route('filament.agent.pages.dashboard');
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
}