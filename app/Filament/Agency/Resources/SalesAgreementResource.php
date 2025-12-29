<?php

namespace App\Filament\Agency\Resources;

use App\Enums\PropertyStatus;
use App\Filament\Agency\Resources\SalesAgreementResource\Pages\CreateSalesAgreement;
use App\Filament\Agency\Resources\SalesAgreementResource\Pages\EditSalesAgreement;
use App\Filament\Agency\Resources\SalesAgreementResource\Pages\ListSalesAgreements;
use App\Filament\Agency\Resources\SalesAgreementResource\Pages\ViewSalesAgreement;
use App\Models\Property;
use App\Models\SalesAgreement;
use App\Models\SalesAgreementTemplate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesAgreementResource extends Resource
{
    protected static ?string $model = SalesAgreement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Sales Agreements';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Agreement Template')
                    ->description('Start with a template to auto-fill terms and conditions')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('template_id')
                                    ->label('Use Template')
                                    ->options(function () {
                                        $agency = Filament::getTenant();
                                        return SalesAgreementTemplate::where('agency_id', $agency?->id)
                                            ->where('is_active', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->placeholder('Select a template (optional)')
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $template = SalesAgreementTemplate::find($state);
                                            if ($template) {
                                                $set('terms_and_conditions', $template->terms_and_conditions);
                                            }
                                        }
                                    })
                                    ->helperText('Templates help you create consistent agreement terms with merge tags'),

                                Actions::make([
                                    Action::make('manageTemplates')
                                        ->label('Manage Templates')
                                        ->icon('heroicon-o-cog-6-tooth')
                                        ->color('gray')
                                        ->url(SalesAgreementTemplateResource::getUrl('index'))
                                        ->openUrlInNewTab(),
                                ])
                                    ->alignEnd(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Property & Parties')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('property_id')
                                    ->label('Property')
                                    ->default(fn () => request()->get('property'))
                                    ->relationship('property', 'title', function (Builder $query) {
                                        $agency = Filament::getTenant();
                                        return $query
                                            ->where('listing_type', 'sale')
                                            ->where('status', PropertyStatus::SOLD->value)
                                            ->where('agency_id', $agency?->id);
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $property = Property::find($state);
                                        if ($property) {
                                            $owner = $property->owner;
                                            $set('seller_name', $owner?->name);
                                            $set('seller_email', $owner?->email);
                                            $set('seller_phone', $owner?->phone);
                                            $set('seller_address', $owner?->address);
                                            $set('sale_price', $property->price);
                                        }
                                    }),

                                Select::make('buyer_user_id')
                                    ->label('Link Buyer User (optional)')
                                    ->relationship('buyer', 'name', function (Builder $query) {
                                        return $query->where('user_type', 'customer');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if (! $state) {
                                            return;
                                        }

                                        $buyer = \App\Models\User::find($state);
                                        if ($buyer) {
                                            $set('buyer_name', $buyer->name);
                                            $set('buyer_email', $buyer->email);
                                            $set('buyer_phone', $buyer->phone);
                                        }
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('seller_name')
                                    ->label('Seller Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('seller_email')
                                    ->label('Seller Email')
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('seller_phone')
                                    ->label('Seller Phone')
                                    ->maxLength(50),

                                TextInput::make('seller_address')
                                    ->label('Seller Address')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('buyer_name')
                                    ->label('Buyer Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('buyer_email')
                                    ->label('Buyer Email')
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('buyer_phone')
                                    ->label('Buyer Phone')
                                    ->maxLength(50),

                                TextInput::make('buyer_address')
                                    ->label('Buyer Address')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Sale Details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('sale_price')
                                    ->label('Sale Price')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, Get $get): void {
                                        self::syncBalanceAmount($set, $get);
                                    })
                                    ->required(),

                                TextInput::make('deposit_amount')
                                    ->label('Deposit Amount')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->reactive()
                                    ->afterStateUpdated(function (Set $set, Get $get): void {
                                        self::syncBalanceAmount($set, $get);
                                    }),

                                TextInput::make('balance_amount')
                                    ->label('Balance Amount')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->helperText('Auto-calculated from sale price and deposit'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                DatePicker::make('signed_date')
                                    ->label('Date Signed')
                                    ->default(now()),

                                DatePicker::make('closing_date')
                                    ->label('Closing Date'),

                                Select::make('status')
                                    ->label('Agreement Status')
                                    ->options(SalesAgreement::getStatuses())
                                    ->default(SalesAgreement::STATUS_DRAFT),
                            ]),
                    ]),

                Section::make('Terms & Conditions')
                    ->columnSpanFull()
                    ->schema([
                        RichEditor::make('terms_and_conditions')
                            ->label('Agreement Terms')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->mergeTags(array_flip(SalesAgreementTemplate::getAvailableVariables()))
                            ->activePanel('mergeTags')
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('buyer_name')
                    ->label('Buyer')
                    ->getStateUsing(fn (SalesAgreement $record) => $record->buyer?->name ?? $record->buyer_name ?? 'Unknown')
                    ->searchable(),

                TextColumn::make('sale_price')
                    ->label('Sale Price')
                    ->formatStateUsing(fn ($state) => formatNaira($state ?? 0))
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        SalesAgreement::STATUS_DRAFT => 'gray',
                        SalesAgreement::STATUS_SIGNED => 'info',
                        SalesAgreement::STATUS_COMPLETED => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('signed_date')
                    ->label('Signed')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(SalesAgreement::getStatuses()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListSalesAgreements::route('/'),
            'create' => CreateSalesAgreement::route('/create'),
            'view' => ViewSalesAgreement::route('/{record}'),
            'edit' => EditSalesAgreement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $agency = Filament::getTenant();
        $query = parent::getEloquentQuery();

        if (! $agency) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('agency_id', $agency->id);
    }

    private static function syncBalanceAmount(Set $set, Get $get): void
    {
        $salePrice = (float) ($get('sale_price') ?? 0);
        $deposit = (float) ($get('deposit_amount') ?? 0);

        if ($salePrice > 0) {
            $balance = max($salePrice - $deposit, 0);
            $set('balance_amount', $balance);
        }
    }
}
