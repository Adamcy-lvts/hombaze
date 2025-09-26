<?php

namespace App\Http\Controllers;

use App\Models\RentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptViewController extends Controller
{
    /**
     * Universal receipt viewer that redirects based on user type
     */
    public function view(Request $request, $receiptId)
    {
        // Find the receipt
        $receipt = RentPayment::findOrFail($receiptId);

        // Check if user is authenticated
        if (!Auth::check()) {
            // If not authenticated, redirect to login with intended URL
            return redirect()->route('login')->with('intended', $request->fullUrl());
        }

        $user = Auth::user();

        // Determine user type and redirect to appropriate panel
        if ($user->hasRole('landlord') || $user->hasRole('property_owner')) {
            // Check if user owns this receipt
            if ($receipt->landlord_id === $user->id) {
                return redirect()->route('filament.landlord.resources.rent-payments.view-receipt', $receipt);
            }
        }

        if ($user->hasRole('tenant')) {
            // Check if user is the tenant for this receipt
            if ($receipt->tenant_id === $user->id) {
                return redirect()->route('filament.tenant.resources.rent-payments.view-receipt', $receipt);
            }
        }

        if ($user->hasRole('agent')) {
            // Check if agent has access to this property/receipt
            if ($receipt->lease && $receipt->lease->property &&
                ($receipt->lease->property->agent_id === $user->id ||
                 ($receipt->lease->property->agency_id && $user->agency_id === $receipt->lease->property->agency_id))) {
                // For now, redirect agents to landlord view (can be updated when agent panel has receipts)
                return redirect()->route('filament.landlord.resources.rent-payments.view-receipt', $receipt);
            }
        }

        if ($user->hasRole('agency_owner')) {
            // Check if agency owns this property
            if ($receipt->lease && $receipt->lease->property &&
                $receipt->lease->property->agency_id === $user->agency_id) {
                // For now, redirect agency owners to landlord view (can be updated when agency panel has receipts)
                return redirect()->route('filament.landlord.resources.rent-payments.view-receipt', $receipt);
            }
        }

        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            // Admins can view any receipt - redirect to landlord view for now
            return redirect()->route('filament.landlord.resources.rent-payments.view-receipt', $receipt);
        }

        // If no access found, show unauthorized
        abort(403, 'You do not have permission to view this receipt.');
    }
}