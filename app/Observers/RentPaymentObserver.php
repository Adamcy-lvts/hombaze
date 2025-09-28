<?php

namespace App\Observers;

use App\Models\RentPayment;

class RentPaymentObserver
{
    /**
     * Handle the RentPayment "created" event.
     */
    public function created(RentPayment $rentPayment): void
    {
        //
    }

    /**
     * Handle the RentPayment "updated" event.
     */
    public function updated(RentPayment $rentPayment): void
    {
        // Check if status changed to 'paid'
        if ($rentPayment->isDirty('status') && $rentPayment->status === 'paid') {
            $this->updatePropertyStatus($rentPayment);
        }
    }

    /**
     * Update property status based on payment and listing type
     */
    private function updatePropertyStatus(RentPayment $rentPayment): void
    {
        $property = $rentPayment->property;

        if (!$property) {
            return;
        }

        // Update property status based on listing type
        if ($property->listing_type === 'rent') {
            $property->update(['status' => 'rented']);
        } elseif ($property->listing_type === 'sale') {
            $property->update(['status' => 'sold']);
        }
    }

    /**
     * Handle the RentPayment "deleted" event.
     */
    public function deleted(RentPayment $rentPayment): void
    {
        //
    }

    /**
     * Handle the RentPayment "restored" event.
     */
    public function restored(RentPayment $rentPayment): void
    {
        //
    }

    /**
     * Handle the RentPayment "force deleted" event.
     */
    public function forceDeleted(RentPayment $rentPayment): void
    {
        //
    }
}
