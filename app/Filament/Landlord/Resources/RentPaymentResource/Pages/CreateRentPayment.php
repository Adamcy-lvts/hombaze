<?php

namespace App\Filament\Landlord\Resources\RentPaymentResource\Pages;

use App\Filament\Landlord\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRentPayment extends CreateRecord
{
    protected static string $resource = RentPaymentResource::class;

    /**
     * Calculate balance_due and net_amount before creating the record
     * This ensures values are set even if the reactive form callbacks didn't fire
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $amount = (float) ($data['amount'] ?? 0);
        $lateFee = (float) ($data['late_fee'] ?? 0);
        $discount = (float) ($data['discount'] ?? 0);
        $deposit = (float) ($data['deposit'] ?? 0);
        
        $netAmount = $amount + $lateFee - $discount;
        $balanceDue = max(0, $netAmount - $deposit);
        
        // Only set if not already calculated by reactive callbacks
        $data['net_amount'] = $data['net_amount'] ?? $netAmount;
        $data['balance_due'] = $data['balance_due'] ?? $balanceDue;
        
        return $data;
    }
}
