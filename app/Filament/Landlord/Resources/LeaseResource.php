<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\LeaseResource\Pages;
use App\Models\Lease;
use App\Models\LeaseTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Leases';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lease Template')
                    ->description('Start with a template to auto-fill terms and conditions')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('template_id')
                                    ->label('Use Template')
                                    ->options(function () {
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
                                                $property = \App\Models\Property::find($get('property_id'));
                                                $tenant = \App\Models\Tenant::find($get('tenant_id'));
                                                
                                                if ($property && $tenant) {
                                                    $substitutedTerms = $template->substituteVariables([
                                                        'property_title' => $property->title,
                                                        'property_address' => $property->address,
                                                        'property_type' => $property->propertyType->name ?? '',
                                                        'property_subtype' => $property->propertySubtype->name ?? '',
                                                        'landlord_name' => Auth::user()->name,
                                                        'landlord_email' => Auth::user()->email,
                                                        'tenant_name' => $tenant->name,
                                                        'tenant_email' => $tenant->email,
                                                        'lease_start_date' => $get('start_date') ? \Carbon\Carbon::parse($get('start_date'))->format('F j, Y') : '',
                                                        'lease_end_date' => $get('end_date') ? \Carbon\Carbon::parse($get('end_date'))->format('F j, Y') : '',
                                                        'lease_duration_months' => $get('start_date') && $get('end_date') ? \Carbon\Carbon::parse($get('start_date'))->diffInMonths(\Carbon\Carbon::parse($get('end_date'))) : '',
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

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('manageTemplates')
                                        ->label('Manage Templates')
                                        ->icon('heroicon-o-cog-6-tooth')
                                        ->color('gray')
                                        ->url(route('filament.landlord.resources.lease-templates.index'))
                                        ->openUrlInNewTab(),
                                ])
                                    ->alignEnd(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Basic Lease Information')
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
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $property = \App\Models\Property::find($state);
                                            if ($property) {
                                                $set('yearly_rent', $property->price);
                                            }
                                        }
                                    }),

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
                                    ->label('Lease Start Date')
                                    ->required()
                                    ->native(false)
                                    ->default(now()),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Lease End Date')
                                    ->required()
                                    ->native(false)
                                    ->default(now()->addYear()),
                            ]),
                    ]),

                Forms\Components\Section::make('Financial Terms')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('yearly_rent')
                                    ->label('Annual Rent')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₦')
                                    ->minValue(0)
                                    ->readonly()
                                    ->helperText('Automatically populated from selected property'),

                                Forms\Components\Select::make('payment_frequency')
                                    ->label('Payment Terms')
                                    ->options([
                                        'annually' => 'Annually (Full Payment)',
                                        'biannually' => 'Bi-annually (2 payments)',
                                        'quarterly' => 'Quarterly (4 payments)',
                                    ])
                                    ->required()
                                    ->default('annually'),
                            ]),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Agreement Status')
                                    ->options(Lease::getStatuses())
                                    ->required()
                                    ->default('draft'),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('signed_date')
                                    ->label('Date Signed')
                                    ->native(false),

                                Forms\Components\Toggle::make('renewal_option')
                                    ->label('Renewal Available')
                                    ->default(false)
                                    ->helperText('Can this lease be renewed?'),
                            ]),

                        Forms\Components\RichEditor::make('terms_and_conditions')
                            ->label('Terms & Conditions')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ])
                            ->default('
<h3>Standard Lease Terms</h3>
<ol>
<li>The tenant agrees to pay rent <strong>as specified</strong> in the financial terms above.</li>
<li>The tenant shall use the premises <strong>solely for residential purposes</strong> and shall not conduct any business activities without prior written consent from the landlord.</li>
<li>The tenant shall <strong>maintain the premises in good condition</strong> and shall be responsible for any damages beyond normal wear and tear.</li>
<li>The tenant shall <strong>not sublease, assign, or transfer</strong> any rights under this agreement without written consent from the landlord.</li>
<li>The tenant shall <strong>comply with all applicable laws, regulations, and community rules</strong> and shall not engage in any illegal activities on the premises.</li>
<li>The landlord shall <strong>maintain the structural integrity</strong> of the property and ensure all major systems (plumbing, electrical, etc.) are in working order.</li>
<li>Either party may <strong>terminate this agreement with 30 days written notice</strong>, subject to applicable local laws and regulations.</li>
<li>This lease renewal option will be determined based on the renewal option setting for this specific lease agreement.</li>
</ol>
                            ')
                            ->helperText('Customize the terms and conditions for this lease agreement'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('property.owner', function (Builder $query) {
                    $query->where('user_id', Auth::id());
                });
            })
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
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('yearly_rent')
                    ->label('Annual Rent')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_frequency')
                    ->label('Payment Terms')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'annually' => 'success',
                        'biannually' => 'warning',
                        'quarterly' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'expired' => 'danger',
                        'terminated' => 'warning',
                        'renewed' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Lease::getStatuses()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Agreement'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'view' => Pages\ViewLease::route('/{record}'),
            'edit' => Pages\EditLease::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('property.owner', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }
}