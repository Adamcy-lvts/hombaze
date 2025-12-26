<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingBundlePurchaseResource\Pages;
use App\Models\ListingAddon;
use App\Models\ListingBundlePurchase;
use App\Models\ListingCreditAccount;
use App\Models\ListingCreditTransaction;
use App\Models\ListingPackage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ListingBundlePurchaseResource extends Resource
{
    protected static ?string $model = ListingBundlePurchase::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string | \UnitEnum | null $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Listing Purchases';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Purchase')
                                    ->schema([
                                        TextInput::make('owner_type')
                                            ->disabled(),
                                        TextInput::make('owner_id')
                                            ->disabled(),
                                        TextInput::make('product_type')
                                            ->disabled(),
                                        TextInput::make('product_id')
                                            ->disabled(),
                                        TextInput::make('amount')
                                            ->numeric()
                                            ->prefix('₦'),
                                        TextInput::make('currency'),
                                        TextInput::make('status'),
                                        TextInput::make('paystack_reference')
                                            ->label('Reference'),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make('Dates')
                                    ->schema([
                                        DateTimePicker::make('paid_at'),
                                        DateTimePicker::make('created_at')
                                            ->disabled(),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 1]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('owner_type')
                    ->label('Owner Type')
                    ->formatStateUsing(fn ($state) => class_exists($state) ? class_basename($state) : $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('owner_id')
                    ->label('Owner')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->owner?->name
                            ?? $record->owner?->agency_name
                            ?? $record->owner?->company_name
                            ?? 'Unknown';
                    })
                    ->searchable(),
                TextColumn::make('product_type')
                    ->label('Product')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->product_type === 'package') {
                            return ListingPackage::find($record->product_id)?->name ?? 'Package';
                        }
                        if ($record->product_type === 'addon') {
                            return ListingAddon::find($record->product_id)?->name ?? 'Add-on';
                        }
                        return $record->product_type;
                    })
                    ->badge(),
                TextColumn::make('listing_credits_granted')
                    ->label('Listing Credits')
                    ->state(function (ListingBundlePurchase $record): string {
                        $product = $record->product_type === 'addon'
                            ? ListingAddon::find($record->product_id)
                            : ListingPackage::find($record->product_id);
                        $credits = (int) ($product?->listing_credits ?? 0);
                        return (string) $credits;
                    }),
                TextColumn::make('featured_credits_granted')
                    ->label('Featured Credits')
                    ->state(function (ListingBundlePurchase $record): string {
                        $product = $record->product_type === 'addon'
                            ? ListingAddon::find($record->product_id)
                            : ListingPackage::find($record->product_id);
                        $credits = (int) ($product?->featured_credits ?? 0);
                        return (string) $credits;
                    }),
                TextColumn::make('credits_used_since_purchase')
                    ->label('Credits Used')
                    ->state(function (ListingBundlePurchase $record): string {
                        $owner = $record->owner;
                        if (!$owner) {
                            return '0';
                        }
                        $account = ListingCreditAccount::where('owner_type', $owner->getMorphClass())
                            ->where('owner_id', $owner->getKey())
                            ->first();
                        if (!$account) {
                            return '0';
                        }
                        $from = $record->paid_at ?? $record->created_at;
                        $used = ListingCreditTransaction::where('listing_credit_account_id', $account->id)
                            ->where('credits', '<', 0)
                            ->where('created_at', '>=', $from)
                            ->sum('credits');
                        return (string) abs((int) $used);
                    }),
                TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListingBundlePurchases::route('/'),
            'create' => Pages\CreateListingBundlePurchase::route('/create'),
            'view' => Pages\ViewListingBundlePurchase::route('/{record}'),
            'edit' => Pages\EditListingBundlePurchase::route('/{record}/edit'),
        ];
    }
}
