<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\PropertyOwnerResource\Pages;
use App\Models\PropertyOwner;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;

class PropertyOwnerResource extends Resource
{
    protected static ?string $model = PropertyOwner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?string $modelLabel = 'Property Owner Profile';

    protected static ?string $pluralModelLabel = 'Property Owner Profile';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Profile';

    public static function getEloquentQuery(): Builder
    {
        // Only show the PropertyOwner profile linked to the current authenticated user
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->description('Your basic profile information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Owner Type')
                                    ->options([
                                        'individual' => 'Individual',
                                        'company' => 'Company',
                                        'trust' => 'Trust',
                                        'government' => 'Government'
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->default('individual'),

                                Forms\Components\TextInput::make('tax_id')
                                    ->label('Tax ID / Registration Number')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required(fn ($get) => $get('type') === 'individual')
                                    ->hidden(fn ($get) => $get('type') !== 'individual')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required(fn ($get) => $get('type') === 'individual')
                                    ->hidden(fn ($get) => $get('type') !== 'individual')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\TextInput::make('company_name')
                            ->label('Company/Organization Name')
                            ->required(fn ($get) => $get('type') !== 'individual')
                            ->hidden(fn ($get) => $get('type') === 'individual')
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->hidden(fn ($get) => $get('type') !== 'individual'),
                    ]),

                Section::make('Contact Information')
                    ->description('How tenants and agents can reach you')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->label('Street Address')
                            ->rows(3)
                            ->maxLength(500),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('state_id')
                                    ->label('State')
                                    ->options(State::pluck('name', 'id'))
                                    ->reactive()
                                    ->searchable(),

                                Forms\Components\Select::make('city_id')
                                    ->label('City')
                                    ->options(fn ($get) => $get('state_id')
                                        ? City::where('state_id', $get('state_id'))->pluck('name', 'id')
                                        : []
                                    )
                                    ->reactive()
                                    ->searchable(),

                                Forms\Components\Select::make('area_id')
                                    ->label('Area')
                                    ->options(fn ($get) => $get('city_id')
                                        ? Area::where('city_id', $get('city_id'))->pluck('name', 'id')
                                        : []
                                    )
                                    ->searchable(),
                            ]),

                        Forms\Components\TextInput::make('country')
                            ->label('Country')
                            ->default('Nigeria')
                            ->maxLength(255),

                        Forms\Components\Select::make('preferred_communication')
                            ->label('Preferred Communication Method')
                            ->options([
                                'email' => 'Email',
                                'phone' => 'Phone',
                                'whatsapp' => 'WhatsApp',
                                'sms' => 'SMS'
                            ])
                            ->default('email'),
                    ]),

                Section::make('Documents & Verification')
                    ->description('Upload documents for profile verification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('profile_photo')
                                    ->label('Profile Photo')
                                    ->image()
                                    ->avatar()
                                    ->directory('property-owners/photos')
                                    ->visibility('private'),

                                FileUpload::make('id_document')
                                    ->label('ID Document (NIN, Passport, etc.)')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->directory('property-owners/documents')
                                    ->visibility('private'),
                            ]),

                        FileUpload::make('proof_of_address')
                            ->label('Proof of Address')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->directory('property-owners/documents')
                            ->visibility('private'),
                    ]),

                Section::make('Additional Information')
                    ->description('Optional notes and information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Any additional information you\'d like to share...'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Profile Active')
                            ->default(true)
                            ->helperText('Disable this if you want to temporarily deactivate your profile'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('Photo')
                    ->circular(),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name', 'company_name']),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'individual' => 'success',
                        'company' => 'info',
                        'trust' => 'warning',
                        'government' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->copyable(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for profile
            ])
            ->emptyStateHeading('No Profile Found')
            ->emptyStateDescription('You don\'t have a property owner profile yet. Create one to manage your properties.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Profile'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyOwners::route('/'),
            'create' => Pages\CreatePropertyOwner::route('/create'),
            'edit' => Pages\EditPropertyOwner::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasRole(['landlord', 'property_owner']);
    }

    public static function canCreate(): bool
    {
        // Only allow creation if user doesn't already have a PropertyOwner profile
        return Auth::user()->hasRole(['landlord', 'property_owner']) &&
               !PropertyOwner::where('user_id', Auth::id())->exists();
    }
}