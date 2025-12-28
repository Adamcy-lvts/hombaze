<?php

namespace App\Filament\Landlord\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Models\RentPayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentPaymentsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RentPayment::query()
                    ->where('landlord_id', Auth::id())
                    ->with(['lease.property', 'tenant'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('lease.property.title')
                    ->label('Property')
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 25 ? $state : null;
                    }),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable(),

                TextColumn::make('net_amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'partial' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'secondary',
                        'refunded' => 'info',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn (RentPayment $record): string => route('filament.landlord.resources.rent-payments.view-receipt', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
                    
                Action::make('downloadReceipt')
                    ->label('Receipt')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (RentPayment $record) => route('filament.landlord.resources.rent-payments.view-receipt', ['record' => $record]) . '?download=png')
                    ->openUrlInNewTab(false)
                    ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
            ])
            ->heading('Recent Payments')
            ->description('Latest payment records and receipts')
            ->emptyStateHeading('No payments found')
            ->emptyStateDescription('Payment records will appear here once created.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
