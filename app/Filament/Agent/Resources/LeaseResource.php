<?php

namespace App\Filament\Agent\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use App\Models\Property;
use App\Models\Tenant;
use Carbon\Carbon;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Agent\Resources\LeaseResource\Pages;
use App\Models\Lease;
use App\Models\LeaseTemplate;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Leases';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lease Template')
                    ->description('Start with a template to auto-fill terms and conditions')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('template_id')
                                    ->label('Use Template')
                                    ->options(function () {
                                        // Adapting for Agent: Might need to find templates relevant to the agent or the property owner
                                        // For now, keeping logic similar to Landlord as requested, using Auth::id()
                                        // Assuming Agent might have their own templates or this logic needs adjustment later
                                        return LeaseTemplate::where('landlord_id', Auth::id())
                                            ->where('is_active', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->placeholder('Select a template (optional)')
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state) {
                                            $template = LeaseTemplate::find($state);
                                            if ($template) {
                                                // Auto-fill form fields from template defaults
                                                $set('payment_frequency', $template->default_payment_frequency);
                                                $set('renewal_option', $template->default_renewal_option);
                                                
                                                // Generate dynamic terms and conditions
                                                $property = Property::find($get('property_id'));
                                                $tenant = Tenant::find($get('tenant_id'));
                                                
                                                if ($property && $tenant) {
                                                    $substitutedTerms = $template->substituteVariables([
                                                        'property_title' => $property->title,
                                                        'property_address' => $property->address,
                                                        'property_type' => $property->propertyType->name ?? '',
                                                        'property_subtype' => $property->propertySubtype->name ?? '',
                                                        'property_bedrooms' => $property->bedrooms > 0 ? $property->bedrooms : '',
                                                        'property_city' => $property->city->name ?? '',
                                                        'property_state' => $property->state->name ?? '',
                                                        'property_area' => $property->area->name ?? '',
                                                        'landlord_name' => Auth::user()->name,
                                                        'landlord_email' => Auth::user()->email,
                                                        'landlord_phone' => Auth::user()->phone ?? '',
                                                        'landlord_address' => Auth::user()->address ?? '',
                                                        'tenant_name' => $tenant->name,
                                                        'tenant_email' => $tenant->email,
                                                        'tenant_phone' => $tenant->phone ?? '',
                                                        'tenant_address' => $tenant->address ?? '',
                                                        'lease_start_date' => $get('start_date') ? Carbon::parse($get('start_date'))->format('F j, Y') : '',
                                                        'lease_end_date' => $get('end_date') ? Carbon::parse($get('end_date'))->format('F j, Y') : '',
                                                        'lease_duration_months' => $get('start_date') && $get('end_date') ? Carbon::parse($get('start_date'))->diffInMonths(Carbon::parse($get('end_date'))) : '',
                                                        'rent_amount' => $get('yearly_rent'),
                                                        'payment_frequency' => $get('payment_frequency'),
                                                        'renewal_option' => $get('renewal_option'),
                                                    ]);
                                                    $set('terms_and_conditions', $substitutedTerms);
                                                } else {
                                                    $set('terms_and_conditions', $template->terms_and_conditions);
                                                }
                                            }
                                        }
                                    })
                                    ->helperText('Templates help you create consistent lease terms with automatic variable substitution'),

                                // Removed the "Manage Templates" action here as it points to Landlord route. 
                                // Agent might not have access to manage templates directly here or needs a different route.
                                // If needed, we can add it back pointing to Agent template resource if it exists.
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Basic Lease Information')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('property_id')
                                    ->label('Property')
                                    // Modified relationship query for Agent: Properties they manage? 
                                    // Keeping similar to Landlord for now but removing strictly owner check if Agent doesn't 'own' it.
                                    // Actually, if Agent creates a lease, they pick from their properties.
                                    // Assuming Property model has scope or we filter by what the agent can access.
                                    ->relationship('property', 'title', function (Builder $query) {
                                        return $query->whereHas('agent', function ($q) {
                                            $q->where('user_id', Auth::id());
                                        });
                                    }) 
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $property = Property::find($state);
                                            if ($property) {
                                                $set('yearly_rent', $property->price);
                                            }
                                        }
                                    }),

                                Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'first_name', function (Builder $query) {
                                        return $query->whereHas('agent', function ($q) {
                                            $q->where('user_id', Auth::id());
                                        });
                                    }) // Filtered for Agent
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Lease Start Date')
                                    ->required()
                                    ->native(false)
                                    ->default(now()),

                                DatePicker::make('end_date')
                                    ->label('Lease End Date')
                                    ->required()
                                    ->native(false)
                                    ->default(now()->addYear()),
                            ]),
                    ]),

                Section::make('Financial Terms')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('yearly_rent')
                                    ->label('Annual Rent')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₦')
                                    ->minValue(0)
                                    ->readonly()
                                    ->helperText('Automatically populated from selected property'),

                                Select::make('payment_frequency')
                                    ->label('Payment Terms')
                                    ->options([
                                        'annually' => 'Annually (Full Payment)',
                                        'biannually' => 'Bi-annually (2 payments)',
                                        'quarterly' => 'Quarterly (4 payments)',
                                    ])
                                    ->required()
                                    ->default('annually'),
                            ]),

                        Grid::make(1)
                            ->schema([
                                Select::make('status')
                                    ->label('Agreement Status')
                                    ->options(Lease::getStatuses())
                                    ->required()
                                    ->default('draft'),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('signed_date')
                                    ->label('Date Signed')
                                    ->native(false),

                                Toggle::make('renewal_option')
                                    ->label('Renewal Available')
                                    ->default(false)
                                    ->helperText('Can this lease be renewed?'),
                            ]),

                        RichEditor::make('terms_and_conditions')
                            ->label('Terms & Conditions')
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
                            ->mergeTags(array_flip(LeaseTemplate::getAvailableVariables()))
                            ->activePanel('mergeTags')
                            ->default(LeaseTemplate::getDefaultContent())
                            ->helperText('Customize the terms and conditions for this lease agreement'),

                        Textarea::make('notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('property', function (Builder $query) {
                $query->whereHas('agent', function (Builder $q) {
                    $q->where('user_id', Auth::id());
                });
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('yearly_rent')
                    ->label('Annual Rent')
                    ->money('NGN')
                    ->sortable(),

                TextColumn::make('payment_frequency')
                    ->label('Payment Terms')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'annually' => 'success',
                        'biannually' => 'warning',
                        'quarterly' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'expired' => 'danger',
                        'terminated' => 'warning',
                        'renewed' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Lease::getStatuses()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListLeases::route('/'),
            'create' => Pages\CreateLease::route('/create'),
            'edit' => Pages\EditLease::route('/{record}/edit'),
            'view' => Pages\ViewLease::route('/{record}'),
        ];
    }
}
