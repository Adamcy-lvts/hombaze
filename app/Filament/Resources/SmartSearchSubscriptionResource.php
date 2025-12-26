<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmartSearchSubscriptionResource\Pages;
use App\Models\SmartSearchSubscription;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SmartSearchSubscriptionResource extends Resource
{
    protected static ?string $model = SmartSearchSubscription::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bell';

    protected static string | \UnitEnum | null $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'SmartSearch Subscriptions';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Subscription')
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship('user', 'email')
                                            ->searchable()
                                            ->required(),
                                        TextInput::make('tier')
                                            ->required(),
                                        TextInput::make('searches_limit')
                                            ->numeric(),
                                        TextInput::make('searches_used')
                                            ->numeric(),
                                        TextInput::make('duration_days')
                                            ->numeric(),
                                        TextInput::make('amount_paid')
                                            ->numeric()
                                            ->prefix('₦'),
                                        TextInput::make('payment_method'),
                                        TextInput::make('payment_status'),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['default' => 1, 'lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make('Dates')
                                    ->schema([
                                        DateTimePicker::make('paid_at'),
                                        DateTimePicker::make('starts_at'),
                                        DateTimePicker::make('expires_at'),
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
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('tier')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $name = DB::table('smart_search_plans')->where('slug', $state)->value('name');
                        return $name ?: $state;
                    }),
                TextColumn::make('payment_status')
                    ->badge()
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ]),
                TextColumn::make('searches_used')
                    ->label('Used'),
                TextColumn::make('searches_limit')
                    ->label('Limit'),
                TextColumn::make('amount_paid')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_renewal')
                    ->boolean()
                    ->label('Renewal')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('payment_status', 'paid')->where('expires_at', '>', now()))
                    ->label('Active'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmartSearchSubscriptions::route('/'),
            'create' => Pages\CreateSmartSearchSubscription::route('/create'),
            'view' => Pages\ViewSmartSearchSubscription::route('/{record}'),
            'edit' => Pages\EditSmartSearchSubscription::route('/{record}/edit'),
        ];
    }
}
