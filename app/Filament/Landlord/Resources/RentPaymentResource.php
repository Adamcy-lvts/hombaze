<?php

namespace App\Filament\Landlord\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Landlord\Resources\RentPaymentResource\Pages\ListRentPayments;
use App\Filament\Landlord\Resources\RentPaymentResource\Pages\CreateRentPayment;
use App\Filament\Landlord\Resources\RentPaymentResource\Pages\EditRentPayment;
use App\Filament\Landlord\Resources\RentPaymentResource\Pages\ViewReceipt;
use App\Filament\Landlord\Resources\RentPaymentResource\Pages;
use App\Filament\Landlord\Resources\RentPaymentResource\RelationManagers;
use App\Models\RentPayment;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Financial Management';

    protected static ?string $navigationLabel = 'Rent Payments';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('lease_id')
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
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $lease = Lease::find($state);
                                            if ($lease) {
                                                $set('tenant_id', $lease->tenant_id);
                                                $set('property_id', $lease->property_id);
                                                $set('landlord_id', Auth::id());
                                                $set('amount', $lease->yearly_rent);
                                            }
                                        }
                                    }),

                                Hidden::make('landlord_id')
                                    ->default(Auth::id())
                                    ->required(),

                                Hidden::make('processed_by')
                                    ->default(Auth::id()),

                                Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'first_name', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('property_id')
                                    ->label('Property')
                                    ->relationship('property', 'title', function (Builder $query) {
                                        return $query->whereHas('owner', function (Builder $query) {
                                            $query->where('user_id', Auth::id());
                                        });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('receipt_number')
                                    ->label('Receipt Number')
                                    ->default(fn () => 'RCP-' . strtoupper(Str::random(8)))
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('amount')
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

                                TextInput::make('late_fee')
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

                                TextInput::make('discount')
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

                        Grid::make(2)
                            ->schema([
                                TextInput::make('deposit')
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

                                TextInput::make('balance_due')
                                    ->label('Balance Due')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->readonly()
                                    ->dehydrated(),
                            ]),

                        Grid::make(1)
                            ->schema([
                                TextInput::make('net_amount')
                                    ->label('Net Amount')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->readonly()
                                    ->dehydrated(),
                            ]),
                    ]),

                Section::make('Payment Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('payment_date')
                                    ->required()
                                    ->default(now()),

                                DatePicker::make('due_date')
                                    ->required(),

                                TextInput::make('payment_for_period')
                                    ->label('Payment For Period')
                                    ->placeholder('e.g., January 2024, Q1 2024'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('payment_method')
                                    ->options(RentPayment::getPaymentMethods())
                                    ->required(),

                                Select::make('status')
                                    ->options(RentPayment::getStatuses())
                                    ->required()
                                    ->default('pending'),

                            ]),
                    ]),

                Section::make('Transaction Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('transaction_reference')
                                    ->maxLength(255),

                                TextInput::make('payment_reference')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('processed_at'),

                                Select::make('processed_by')
                                    ->relationship('processedBy', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Textarea::make('payment_proof')
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
                TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('lease.property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lease_period')
                    ->label('Lease Period')
                    ->formatStateUsing(function (RentPayment $record) {
                        if ($record->lease && $record->lease->start_date && $record->lease->end_date) {
                            return $record->lease->start_date->format('M d, Y') . ' - ' . $record->lease->end_date->format('M d, Y');
                        }
                        return 'N/A';
                    })
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),

                TextColumn::make('late_fee')
                    ->money('NGN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deposit')
                    ->money('NGN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('balance_due')
                    ->label('Balance Due')
                    ->money('NGN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'partial' => 'info',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('payment_method')
                    ->badge(),


                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(RentPayment::getStatuses()),

                SelectFilter::make('payment_method')
                    ->options(RentPayment::getPaymentMethods()),

                SelectFilter::make('payment_period')
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

                Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->overdue())
                    ->label('Overdue Payments'),

                Filter::make('pending')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending'))
                    ->label('Pending Payments'),

                Filter::make('paid')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'paid'))
                    ->label('Paid'),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('viewReceipt')
                        ->label('View Receipt')
                        ->icon('heroicon-o-receipt-percent')
                        ->color('primary')
                        ->url(fn (RentPayment $record) => route('filament.landlord.resources.rent-payments.view-receipt', $record))
                        ->openUrlInNewTab(true)
                        ->visible(fn (RentPayment $record) => in_array($record->status, ['paid', 'partial'])),
                    Action::make('downloadReceipt')
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListRentPayments::route('/'),
            'create' => CreateRentPayment::route('/create'),
            'edit' => EditRentPayment::route('/{record}/edit'),
            'view-receipt' => ViewReceipt::route('/{record}/receipt'),
        ];
    }
}
