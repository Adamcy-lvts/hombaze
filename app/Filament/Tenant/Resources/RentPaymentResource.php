<?php

namespace App\Filament\Tenant\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use App\Filament\Tenant\Resources\RentPaymentResource\Pages\ListRentPayments;
use App\Filament\Tenant\Resources\RentPaymentResource\Pages\ViewRentPayment;
use App\Filament\Tenant\Resources\RentPaymentResource\Pages\ViewReceipt;
use App\Filament\Tenant\Resources\RentPaymentResource\Pages;
use App\Filament\Tenant\Resources\RentPaymentResource\RelationManagers;
use App\Models\RentPayment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Payment History';

    protected static ?string $modelLabel = 'Payment';

    protected static ?string $pluralModelLabel = 'Payment History';

    protected static string | \UnitEnum | null $navigationGroup = 'My Tenancy';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->description('Payment information and receipt details')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('receipt_number')
                                    ->label('Receipt Number')
                                    ->disabled(),
                                    
                                TextInput::make('amount')
                                    ->label('Amount Paid')
                                    ->prefix('₦')
                                    ->numeric()
                                    ->disabled(),
                                    
                                DatePicker::make('payment_date')
                                    ->label('Payment Date')
                                    ->disabled(),
                                    
                                DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->disabled(),
                                    
                                TextInput::make('payment_method')
                                    ->label('Payment Method')
                                    ->disabled(),
                                    
                                TextInput::make('payment_reference')
                                    ->label('Payment Reference')
                                    ->disabled(),
                                    
                                TextInput::make('status')
                                    ->disabled(),
                                    
                                TextInput::make('late_fee')
                                    ->label('Late Fee')
                                    ->prefix('₦')
                                    ->numeric()
                                    ->disabled()
                                    ->visible(fn ($record) => $record && $record->late_fee > 0),
                            ]),
                    ])->collapsible(),

                Section::make('Property Information')
                    ->schema([
                        TextInput::make('property.title')
                            ->label('Property')
                            ->disabled(),
                            
                        TextInput::make('lease.yearly_rent')
                            ->label('Annual Rent')
                            ->prefix('₦')
                            ->numeric()
                            ->disabled(),
                            
                        TextInput::make('payment_for_period')
                            ->label('Payment Period')
                            ->disabled(),
                    ])->collapsible(),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Payment Notes')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ])->collapsible()->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->prefix('₦')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('payment_for_period')
                    ->label('Period')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'partial',
                        'danger' => 'overdue',
                        'gray' => 'pending',
                        'secondary' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucwords($state)),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->toggleable(),

                TextColumn::make('late_fee')
                    ->label('Late Fee')
                    ->prefix('₦')
                    ->numeric()
                    ->toggleable()
                    ->visible(fn ($record) => $record && $record->late_fee > 0),

                TextColumn::make('property.title')
                    ->label('Property')
                    ->toggleable(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'partial' => 'Partial',
                        'overdue' => 'Overdue',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'cash' => 'Cash',
                        'cheque' => 'Cheque',
                        'online' => 'Online Payment',
                        'card' => 'Card Payment',
                    ]),

                Filter::make('payment_date')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View Payment Details'),
                Action::make('view_receipt')
                    ->label('View Receipt')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('primary')
                    ->url(fn (RentPayment $record) => route('filament.tenant.resources.rent-payments.view-receipt', $record))
                    ->openUrlInNewTab(true)
                    ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
                Action::make('download_receipt')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (RentPayment $record) => route('filament.tenant.resources.rent-payments.view-receipt', $record) . '?download=pdf')
                    ->openUrlInNewTab(false)
                    ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
            ])
            ->toolbarActions([
                // No bulk actions for tenant payment view
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        // Get the current user and find their tenant record
        $user = Auth::user();
        $tenant = $user->tenant ?? null;
        
        if (!$tenant) {
            // If no tenant record exists, return empty query
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('tenant_id', $tenant->id)
            ->where('landlord_id', $tenant->landlord_id)
            ->with(['property', 'lease', 'tenant']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRentPayments::route('/'),
            'view' => ViewRentPayment::route('/{record}'),
            'view-receipt' => ViewReceipt::route('/{record}/receipt'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Tenants cannot create payment records
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Tenants cannot edit payment records
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Tenants cannot delete payment records
    }
}
