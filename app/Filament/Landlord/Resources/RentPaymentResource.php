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
                // Recipient & Property Section
                Section::make('Recipient & Property')
                    ->schema([
                        Forms\Components\Toggle::make('is_manual_entry')
                            ->label('Manual Entry (Free-form Receipt)')
                            ->live()
                            ->columnSpanFull()
                            ->helperText('Enable to enter tenant/property details manually instead of selecting from existing records'),

                        // Existing tenant/property selection (when NOT manual entry)
                        Grid::make(2)
                            ->schema([
                                Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'first_name', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                    ->searchable()
                                    ->preload(),

                                Select::make('property_id')
                                    ->label('Property')
                                    ->relationship('property', 'title', function (Builder $query) {
                                        return $query->whereHas('owner', function (Builder $query) {
                                            $query->where('user_id', Auth::id());
                                        });
                                    })
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->visible(fn ($get) => !$get('is_manual_entry')),

                        Select::make('lease_id')
                            ->label('Lease (Optional)')
                            ->relationship('lease', 'id', function (Builder $query) {
                                return $query->where('landlord_id', Auth::id());
                            })
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                $record->property->title . ' - ' . $record->tenant->name
                            )
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
                            })
                            ->visible(fn ($get) => !$get('is_manual_entry')),

                        // Manual entry fields (when IS manual entry)
                        Grid::make(2)
                            ->schema([
                                TextInput::make('manual_tenant_name')
                                    ->label('Recipient Name')
                                    ->required(fn ($get) => $get('is_manual_entry')),

                                TextInput::make('manual_tenant_phone')
                                    ->label('Phone')
                                    ->tel(),
                            ])
                            ->visible(fn ($get) => $get('is_manual_entry')),

                        TextInput::make('manual_property_title')
                            ->label('Property / Description')
                            ->placeholder('e.g., 3BR Flat at Lekki')
                            ->visible(fn ($get) => $get('is_manual_entry'))
                            ->columnSpanFull(),

                        Hidden::make('landlord_id')
                            ->default(Auth::id())
                            ->required(),

                        Hidden::make('processed_by')
                            ->default(Auth::id()),
                    ]),

                // Payment Details Section
                Section::make('Payment Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₦')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        $amount = (float) $state;
                                        $lateFee = (float) ($get('late_fee') ?? 0);
                                        $discount = (float) ($get('discount') ?? 0);
                                        $netAmount = $amount + $lateFee - $discount;
                                        $set('net_amount', $netAmount);
                                        $set('balance_due', 0);
                                    }),

                                Select::make('payment_method')
                                    ->options(RentPayment::getPaymentMethods())
                                    ->required()
                                    ->default('transfer'),

                                Select::make('status')
                                    ->options(RentPayment::getStatuses())
                                    ->required()
                                    ->default('paid'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                DatePicker::make('payment_date')
                                    ->required()
                                    ->default(now()),

                                DatePicker::make('due_date')
                                    ->default(now()),

                                TextInput::make('receipt_number')
                                    ->label('Receipt #')
                                    ->default(fn () => 'RCP-' . strtoupper(Str::random(8)))
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('payment_for')
                                    ->label('Payment For')
                                    ->placeholder('e.g., Rent Payment, Service Charge, Deposit'),

                                TextInput::make('payment_for_period')
                                    ->label('Payment Period')
                                    ->placeholder('e.g., January 2026, Q1 2026'),
                            ]),
                    ]),

                // Financial Adjustments (Collapsible)
                Section::make('Financial Adjustments')
                    ->collapsed()
                    ->schema([
                        Grid::make(4)
                            ->schema([
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
                                        $netAmount = $amount + $lateFee - $discount;
                                        $set('net_amount', $netAmount);
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
                                        $netAmount = $amount + $lateFee - $discount;
                                        $set('net_amount', $netAmount);
                                    }),

                                TextInput::make('deposit')
                                    ->label('Deposit')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->default(0),

                                TextInput::make('balance_due')
                                    ->label('Balance Due')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->default(0),
                            ]),

                        TextInput::make('net_amount')
                            ->label('Net Amount')
                            ->numeric()
                            ->prefix('₦')
                            ->readonly()
                            ->dehydrated(),
                    ]),

                // Period Dates & Notes (Collapsible)
                Section::make('Additional Details')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('custom_start_date')
                                    ->label('Period Start Date')
                                    ->helperText('For free-form receipts without a lease'),

                                DatePicker::make('custom_end_date')
                                    ->label('Period End Date')
                                    ->helperText('For free-form receipts without a lease'),
                            ]),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
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

                TextColumn::make('property_title')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->description(fn (RentPayment $record) => $record->is_manual_entry ? 'Manual Entry' : null),

                TextColumn::make('tenant_name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_for')
                    ->label('Payment For')
                    ->placeholder('Rent Payment')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('lease_period')
                    ->label('Period')
                    ->formatStateUsing(function (RentPayment $record) {
                        // Priority: custom dates > lease dates
                        if ($record->custom_start_date && $record->custom_end_date) {
                            return $record->custom_start_date->format('M d, Y') . ' - ' . $record->custom_end_date->format('M d, Y');
                        }
                        if ($record->lease && $record->lease->start_date && $record->lease->end_date) {
                            return $record->lease->start_date->format('M d, Y') . ' - ' . $record->lease->end_date->format('M d, Y');
                        }
                        return 'N/A';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

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
                ActionGroup::make([
                    Action::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-adjustments-vertical')
                        ->form([
                            Select::make('status')
                                ->label('Payment Status')
                                ->options(RentPayment::getStatuses())
                                ->required(),
                            DatePicker::make('payment_date')
                                ->label('Payment Date')
                                ->default(now())
                                ->required(),
                            Select::make('payment_method')
                                ->label('Payment Method')
                                ->options(RentPayment::getPaymentMethods())
                                ->required(),
                        ])
                        ->action(function (RentPayment $record, array $data): void {
                            $record->update([
                                'status' => $data['status'],
                                'payment_date' => $data['payment_date'],
                                'payment_method' => $data['payment_method'],
                                'processed_by' => Auth::id(),
                            ]);
                        }),
                    EditAction::make(),
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
                    DeleteAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->color('gray'),
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
