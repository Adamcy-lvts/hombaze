<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\RentPaymentResource\Pages;
use App\Filament\Landlord\Resources\RentPaymentResource\RelationManagers;
use App\Models\RentPayment;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?string $navigationLabel = 'Rent Payments';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('lease_id')
                                    ->label('Lease')
                                    ->relationship('lease', 'id', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                                        $record->property->title . ' - ' . $record->tenant->name
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $lease = Lease::find($state);
                                            if ($lease) {
                                                $set('tenant_id', $lease->tenant_id);
                                                $set('property_id', $lease->property_id);
                                                $set('landlord_id', Auth::id());
                                                $set('amount', $lease->monthly_rent);
                                            }
                                        }
                                    }),

                                Forms\Components\Hidden::make('landlord_id')
                                    ->default(Auth::id())
                                    ->required(),

                                Forms\Components\Hidden::make('processed_by')
                                    ->default(Auth::id()),

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

                                Forms\Components\TextInput::make('receipt_number')
                                    ->label('Receipt Number')
                                    ->default(fn () => 'RCP-' . strtoupper(Str::random(8)))
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Payment Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₦')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        $amount = (float) $state;
                                        $lateFee = (float) ($get('late_fee') ?? 0);
                                        $discount = (float) ($get('discount') ?? 0);
                                        $deposit = (float) ($get('deposit') ?? 0);
                                        $netAmount = $amount + $lateFee - $discount;
                                        $balanceDue = $netAmount - $deposit;
                                        $set('net_amount', $netAmount);
                                        $set('balance_due', max(0, $balanceDue));
                                    }),

                                Forms\Components\TextInput::make('late_fee')
                                    ->label('Late Fee')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        $amount = (float) ($get('amount') ?? 0);
                                        $lateFee = (float) $state;
                                        $discount = (float) ($get('discount') ?? 0);
                                        $deposit = (float) ($get('deposit') ?? 0);
                                        $netAmount = $amount + $lateFee - $discount;
                                        $balanceDue = $netAmount - $deposit;
                                        $set('net_amount', $netAmount);
                                        $set('balance_due', max(0, $balanceDue));
                                    }),

                                Forms\Components\TextInput::make('discount')
                                    ->label('Discount')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        $amount = (float) ($get('amount') ?? 0);
                                        $lateFee = (float) ($get('late_fee') ?? 0);
                                        $discount = (float) $state;
                                        $deposit = (float) ($get('deposit') ?? 0);
                                        $netAmount = $amount + $lateFee - $discount;
                                        $balanceDue = $netAmount - $deposit;
                                        $set('net_amount', $netAmount);
                                        $set('balance_due', max(0, $balanceDue));
                                    }),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('deposit')
                                    ->label('Deposit')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        $amount = (float) ($get('amount') ?? 0);
                                        $lateFee = (float) ($get('late_fee') ?? 0);
                                        $discount = (float) ($get('discount') ?? 0);
                                        $deposit = (float) $state;
                                        $netAmount = $amount + $lateFee - $discount;
                                        $balanceDue = $netAmount - $deposit;
                                        $set('balance_due', max(0, $balanceDue));
                                    }),

                                Forms\Components\TextInput::make('balance_due')
                                    ->label('Balance Due')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->readonly()
                                    ->dehydrated(),
                            ]),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('net_amount')
                                    ->label('Net Amount')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->readonly()
                                    ->dehydrated(),
                            ]),
                    ]),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('payment_date')
                                    ->required()
                                    ->default(now()),

                                Forms\Components\DatePicker::make('due_date')
                                    ->required(),

                                Forms\Components\TextInput::make('payment_for_period')
                                    ->label('Payment For Period')
                                    ->placeholder('e.g., January 2024, Q1 2024'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->options(RentPayment::getPaymentMethods())
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->options(RentPayment::getStatuses())
                                    ->required()
                                    ->default('pending'),

                            ]),
                    ]),

                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('transaction_reference')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('payment_reference')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('processed_at'),

                                Forms\Components\Select::make('processed_by')
                                    ->relationship('processedBy', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\Textarea::make('payment_proof')
                            ->label('Payment Proof/Details')
                            ->rows(2)
                            ->maxLength(500),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('lease.property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lease_period')
                    ->label('Lease Period')
                    ->formatStateUsing(function (RentPayment $record) {
                        if ($record->lease && $record->lease->start_date && $record->lease->end_date) {
                            return $record->lease->start_date->format('M d, Y') . ' - ' . $record->lease->end_date->format('M d, Y');
                        }
                        return 'N/A';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('late_fee')
                    ->money('NGN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deposit')
                    ->money('NGN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('balance_due')
                    ->label('Balance Due')
                    ->money('NGN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'partial' => 'info',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),


                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(RentPayment::getStatuses()),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(RentPayment::getPaymentMethods()),

                Tables\Filters\SelectFilter::make('payment_period')
                    ->options([
                        'january' => 'January',
                        'february' => 'February',
                        'march' => 'March',
                        'april' => 'April',
                        'may' => 'May',
                        'june' => 'June',
                        'july' => 'July',
                        'august' => 'August',
                        'september' => 'September',
                        'october' => 'October',
                        'november' => 'November',
                        'december' => 'December',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->overdue())
                    ->label('Overdue Payments'),

                Tables\Filters\Filter::make('pending')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending'))
                    ->label('Pending Payments'),

                Tables\Filters\Filter::make('paid')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'paid'))
                    ->label('Paid'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('viewReceipt')
                        ->label('View Receipt')
                        ->icon('heroicon-o-receipt-percent')
                        ->color('primary')
                        ->url(fn (RentPayment $record) => route('filament.landlord.resources.rent-payments.view-receipt', $record))
                        ->openUrlInNewTab(true)
                        ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
                    Tables\Actions\Action::make('downloadReceipt')
                        ->label('Download PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (RentPayment $record) {
                            return redirect()->to(route('filament.landlord.resources.rent-payments.view-receipt', $record))->with('auto_download', 'pdf');
                        })
                        ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
                ])
                    ->label('Receipt')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
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
            'index' => Pages\ListRentPayments::route('/'),
            'create' => Pages\CreateRentPayment::route('/create'),
            'edit' => Pages\EditRentPayment::route('/{record}/edit'),
            'view-receipt' => Pages\ViewReceipt::route('/{record}/receipt'),
        ];
    }
}
