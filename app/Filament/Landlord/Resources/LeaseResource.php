<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\LeaseResource\Pages;
use App\Filament\Landlord\Resources\LeaseResource\RelationManagers;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Leases';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lease Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('property_id')
                                    ->label('Property')
                                    ->relationship('property', 'title', function (Builder $query) {
                                        return $query->whereHas('owner', function (Builder $query) {
                                            $query->where('user_id', Auth::id());
                                        });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'first_name', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->required(),

                                Forms\Components\DatePicker::make('end_date')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('signed_date'),

                                Forms\Components\DatePicker::make('move_in_date'),
                            ]),
                    ]),

                Forms\Components\Section::make('Financial Terms')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('monthly_rent')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₦'),

                                Forms\Components\TextInput::make('security_deposit')
                                    ->numeric()
                                    ->prefix('₦'),

                                Forms\Components\TextInput::make('service_charge')
                                    ->numeric()
                                    ->prefix('₦'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('legal_fee')
                                    ->numeric()
                                    ->prefix('₦'),

                                Forms\Components\TextInput::make('agency_fee')
                                    ->numeric()
                                    ->prefix('₦'),

                                Forms\Components\TextInput::make('caution_deposit')
                                    ->numeric()
                                    ->prefix('₦'),
                            ]),
                    ]),

                Forms\Components\Section::make('Payment Terms')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('lease_type')
                                    ->options(Lease::getTypes())
                                    ->required(),

                                Forms\Components\Select::make('payment_frequency')
                                    ->options(Lease::getPaymentFrequencies())
                                    ->required(),

                                Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'bank_transfer' => 'Bank Transfer',
                                        'cash' => 'Cash',
                                        'cheque' => 'Cheque',
                                        'card' => 'Card Payment',
                                        'mobile_money' => 'Mobile Money',
                                    ]),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('late_fee_amount')
                                    ->numeric()
                                    ->prefix('₦'),

                                Forms\Components\TextInput::make('grace_period_days')
                                    ->numeric()
                                    ->suffix('days'),

                                Forms\Components\TextInput::make('early_termination_fee')
                                    ->numeric()
                                    ->prefix('₦'),
                            ]),
                    ]),

                Forms\Components\Section::make('Lease Terms & Conditions')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(Lease::getStatuses())
                            ->required()
                            ->default('draft'),

                        Forms\Components\Select::make('renewal_option')
                            ->options([
                                'automatic' => 'Automatic Renewal',
                                'negotiable' => 'Negotiable',
                                'none' => 'No Renewal Option',
                            ]),

                        Forms\Components\Textarea::make('terms_and_conditions')
                            ->rows(4)
                            ->maxLength(5000),

                        Forms\Components\Textarea::make('special_clauses')
                            ->rows(3)
                            ->maxLength(2000),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
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

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('monthly_rent')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'active' => 'success',
                        'expired' => 'danger',
                        'terminated' => 'danger',
                        'renewed' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('lease_type')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Lease::getStatuses()),

                Tables\Filters\SelectFilter::make('lease_type')
                    ->options(Lease::getTypes()),

                Tables\Filters\Filter::make('active_leases')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active'))
                    ->label('Active Leases Only'),

                Tables\Filters\Filter::make('expiring_soon')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('end_date', [now(), now()->addDays(30)]))
                    ->label('Expiring Within 30 Days'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('landlord_id', Auth::id());
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
            'create' => Pages\CreateLease::route('/create'),
            'edit' => Pages\EditLease::route('/{record}/edit'),
        ];
    }
}
