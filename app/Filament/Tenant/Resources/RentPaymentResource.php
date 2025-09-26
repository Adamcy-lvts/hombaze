<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\RentPaymentResource\Pages;
use App\Filament\Tenant\Resources\RentPaymentResource\RelationManagers;
use App\Models\RentPayment;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Payment History';

    protected static ?string $modelLabel = 'Payment';

    protected static ?string $pluralModelLabel = 'Payment History';

    protected static ?string $navigationGroup = 'My Tenancy';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->description('Payment information and receipt details')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('receipt_number')
                                    ->label('Receipt Number')
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount Paid')
                                    ->prefix('₦')
                                    ->numeric()
                                    ->disabled(),
                                    
                                Forms\Components\DatePicker::make('payment_date')
                                    ->label('Payment Date')
                                    ->disabled(),
                                    
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('payment_method')
                                    ->label('Payment Method')
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('payment_reference')
                                    ->label('Payment Reference')
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('status')
                                    ->badge()
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('late_fee')
                                    ->label('Late Fee')
                                    ->prefix('₦')
                                    ->numeric()
                                    ->disabled()
                                    ->visible(fn ($record) => $record && $record->late_fee > 0),
                            ]),
                    ])->collapsible(),

                Forms\Components\Section::make('Property Information')
                    ->schema([
                        Forms\Components\TextInput::make('property.title')
                            ->label('Property')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('lease.monthly_rent')
                            ->label('Monthly Rent')
                            ->prefix('₦')
                            ->numeric()
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('payment_for_period')
                            ->label('Payment Period')
                            ->disabled(),
                    ])->collapsible(),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
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
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->prefix('₦')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_for_period')
                    ->label('Period')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'partial',
                        'danger' => 'overdue',
                        'gray' => 'pending',
                        'secondary' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucwords($state)),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('late_fee')
                    ->label('Late Fee')
                    ->prefix('₦')
                    ->numeric()
                    ->toggleable()
                    ->visible(fn ($record) => $record && $record->late_fee > 0),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'partial' => 'Partial',
                        'overdue' => 'Overdue',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'cash' => 'Cash',
                        'cheque' => 'Cheque',
                        'online' => 'Online Payment',
                        'card' => 'Card Payment',
                    ]),

                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
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
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Payment Details'),
                Tables\Actions\Action::make('view_receipt')
                    ->label('View Receipt')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('primary')
                    ->url(fn (RentPayment $record) => route('filament.tenant.resources.rent-payments.view-receipt', $record))
                    ->openUrlInNewTab(true)
                    ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
                Tables\Actions\Action::make('download_receipt')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (RentPayment $record) => route('filament.tenant.resources.rent-payments.view-receipt', $record) . '?download=pdf')
                    ->openUrlInNewTab(false)
                    ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
            ])
            ->bulkActions([
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
            'index' => Pages\ListRentPayments::route('/'),
            'view' => Pages\ViewRentPayment::route('/{record}'),
            'view-receipt' => Pages\ViewReceipt::route('/{record}/receipt'),
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
