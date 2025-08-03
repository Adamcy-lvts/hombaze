<?php

namespace App\Observers;

use App\Models\Lease;
use App\Models\RentPayment;
use Illuminate\Support\Str;

class LeaseObserver
{
    public function updated(Lease $lease)
    {
        // Check if the lease status changed to 'active' and was previously not active
        if ($lease->isDirty('status') && $lease->status === 'active' && $lease->getOriginal('status') !== 'active') {
            $this->createInitialPaymentRecord($lease);
        }
    }

    private function createInitialPaymentRecord(Lease $lease)
    {
        // Only create if there are no existing payment records for this lease
        if ($lease->rentPayments()->count() === 0) {
            // Calculate the first payment due date based on payment frequency
            $dueDate = $this->calculateFirstDueDate($lease);
            $paymentPeriod = $this->getPaymentPeriod($lease);

            RentPayment::create([
                'lease_id' => $lease->id,
                'tenant_id' => $lease->tenant_id,
                'landlord_id' => $lease->landlord_id,
                'property_id' => $lease->property_id,
                'amount' => $lease->monthly_rent,
                'payment_date' => null, // Not paid yet
                'due_date' => $dueDate,
                'payment_method' => null, // To be filled when payment is made
                'payment_reference' => null,
                'late_fee' => 0,
                'discount' => 0,
                'net_amount' => $lease->monthly_rent,
                'status' => 'pending',
                'payment_for_period' => $paymentPeriod,
                'notes' => 'Initial payment record created automatically upon lease activation.',
                'receipt_number' => 'RCP-' . strtoupper(Str::random(8)),
                'processed_by' => $lease->landlord_id,
            ]);
        }
    }

    private function calculateFirstDueDate(Lease $lease): \Carbon\Carbon
    {
        $startDate = $lease->start_date;

        return match ($lease->payment_frequency) {
            'annually' => $startDate->copy()->addYear(),
            'biannually' => $startDate->copy()->addMonths(6),
            'quarterly' => $startDate->copy()->addMonths(3),
            'monthly' => $startDate->copy()->addMonth(),
            default => $startDate->copy()->addYear(),
        };
    }

    private function getPaymentPeriod(Lease $lease): string
    {
        $startDate = $lease->start_date;

        return match ($lease->payment_frequency) {
            'annually' => $startDate->format('Y') . ' (Full Year)',
            'biannually' => $startDate->format('M Y') . ' - ' . $startDate->copy()->addMonths(6)->subDay()->format('M Y'),
            'quarterly' => $startDate->format('M Y') . ' - ' . $startDate->copy()->addMonths(3)->subDay()->format('M Y'),
            'monthly' => $startDate->format('F Y'),
            default => $startDate->format('Y') . ' (Full Year)',
        };
    }
}