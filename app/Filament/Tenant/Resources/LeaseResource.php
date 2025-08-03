<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\LeaseResource\Pages;
use App\Filament\Tenant\Resources\LeaseResource\RelationManagers;
use App\Models\Lease;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'My Leases';

    protected static ?string $modelLabel = 'Lease Agreement';

    protected static ?string $pluralModelLabel = 'Lease Agreements';

    protected static ?string $navigationGroup = 'My Tenancy';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lease Agreement Details')
                    ->description('Your lease agreement information')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('property.title')
                                    ->label('Property')
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('status')
                                    ->badge()
                                    ->disabled(),
                                    
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Lease Start Date')
                                    ->disabled()
                                    ->native(false),
                                    
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Lease End Date')
                                    ->disabled()
                                    ->native(false),
                                    
                                Forms\Components\TextInput::make('monthly_rent')
                                    ->label('Monthly Rent')
                                    ->prefix('₦')
                                    ->numeric()
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('security_deposit')
                                    ->label('Security Deposit')
                                    ->prefix('₦')
                                    ->numeric()
                                    ->disabled(),
                            ]),
                    ])->collapsible(),

                Forms\Components\Section::make('Payment Information')
                    ->description('Payment terms and conditions')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Forms\Components\Select::make('payment_frequency')
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'quarterly' => 'Quarterly',
                                        'bi_annually' => 'Bi-Annually',
                                        'annually' => 'Annually',
                                    ])
                                    ->disabled(),
                                    
                                Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'bank_transfer' => 'Bank Transfer',
                                        'cash' => 'Cash',
                                        'cheque' => 'Cheque',
                                        'online' => 'Online Payment',
                                    ])
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('late_fee_amount')
                                    ->label('Late Fee')
                                    ->prefix('₦')
                                    ->numeric()
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('grace_period_days')
                                    ->label('Grace Period (Days)')
                                    ->numeric()
                                    ->disabled(),
                            ]),
                    ])->collapsible(),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('terms_and_conditions')
                            ->label('Terms and Conditions')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('special_clauses')
                            ->label('Special Clauses')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'expired',
                        'gray' => 'terminated',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucwords($state)),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('monthly_rent')
                    ->label('Monthly Rent')
                    ->prefix('₦')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Remaining')
                    ->getStateUsing(function (Lease $record): string {
                        if ($record->end_date) {
                            $daysRemaining = now()->diffInDays($record->end_date, false);
                            if ($daysRemaining < 0) {
                                return 'Expired';
                            } elseif ($daysRemaining < 30) {
                                return $daysRemaining . ' days (Renewal Due)';
                            } else {
                                return $daysRemaining . ' days';
                            }
                        }
                        return 'N/A';
                    })
                    ->badge()
                    ->color(fn (Lease $record): string => match (true) {
                        !$record->end_date => 'gray',
                        now()->diffInDays($record->end_date, false) < 0 => 'danger',
                        now()->diffInDays($record->end_date, false) < 30 => 'warning',
                        default => 'success'
                    }),

                Tables\Columns\TextColumn::make('payment_frequency')
                    ->label('Payment')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'expired' => 'Expired',
                        'terminated' => 'Terminated',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Details'),
                Tables\Actions\Action::make('request_renewal')
                    ->label('Request Renewal')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (Lease $record): bool => 
                        $record->status === 'active' && 
                        $record->end_date && 
                        now()->diffInDays($record->end_date, false) <= 60
                    )
                    ->action(function (Lease $record) {
                        // TODO: Implement renewal request logic
                        // This could create a maintenance request or send notification
                    }),
            ])
            ->bulkActions([
                // No bulk actions for tenant lease view
            ])
            ->defaultSort('start_date', 'desc');
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
            ->with(['property', 'tenant', 'landlord']);
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
            'index' => Pages\ListLeases::route('/'),
            'view' => Pages\ViewLease::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Tenants cannot create lease agreements
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Tenants cannot edit lease agreements
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Tenants cannot delete lease agreements
    }
}
