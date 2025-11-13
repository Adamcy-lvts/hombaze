<?php

namespace App\Filament\Agency\Pages\Tenancy;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditAgencyProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Agency Profile';
    }

    public static function getNavigationLabel(): string
    {
        return 'Agency Profile';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Main content area (2/3 width) and Sidebar (1/3 width)
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])
                    ->schema([
                        // Main Content Area (spans 2 columns)
                        Group::make()
                            ->schema([
                                // Basic Agency Information
                                Section::make('Agency Information')
                                    ->description('Basic details about your agency')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Agency Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Enter agency name')
                                                    ->helperText('Your agency\'s official business name')
                                                    ->columnSpanFull(),
                                                    
                                                TextInput::make('email')
                                                    ->label('Business Email')
                                                    ->email()
                                                    ->required()
                                                    ->placeholder('info@youragency.com')
                                                    ->helperText('Primary business email address')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->required()
                                                    ->placeholder('Enter phone number')
                                                    ->helperText('Primary business phone number')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('license_number')
                                                    ->label('Business License Number')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter business license number')
                                                    ->helperText('Official business/real estate license number')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('registration_number')
                                                    ->label('Registration Number')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter registration number')
                                                    ->helperText('Business registration number')
                                                    ->columnSpan(1),
                                            ]),
                                        Textarea::make('description')
                                            ->label('Agency Description')
                                            ->rows(4)
                                            ->maxLength(1000)
                                            ->placeholder('Describe your agency, services, and specializations...')
                                            ->helperText('Brief description of your agency (max 1000 characters)')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Location Information
                                Section::make('Location Details')
                                    ->description('Agency location and address information')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 3,
                                        ])
                                            ->schema([
                                                Select::make('state_id')
                                                    ->label('State')
                                                    ->relationship('state', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set) {
                                                        $set('city_id', null);
                                                        $set('area_id', null);
                                                    })
                                                    ->columnSpan(1),
                                                    
                                                Select::make('city_id')
                                                    ->label('City')
                                                    ->options(fn (Get $get): array => City::query()
                                                        ->where('state_id', $get('state_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set) => $set('area_id', null))
                                                    ->columnSpan(1),
                                                    
                                                Select::make('area_id')
                                                    ->label('Area')
                                                    ->options(fn (Get $get): array => Area::query()
                                                        ->where('city_id', $get('city_id'))
                                                        ->pluck('name', 'id')
                                                        ->toArray())
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(1),
                                            ]),
                                        Textarea::make('address')
                                            ->label('Street Address')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('Enter complete street address')
                                            ->helperText('Full street address including building number and street name')
                                            ->columnSpanFull(),
                                    ])->collapsible(),

                                // Contact & Online Presence
                                Section::make('Online Presence')
                                    ->description('Website and social media information')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('website')
                                                    ->label('Website URL')
                                                    ->url()
                                                    ->placeholder('https://www.youragency.com')
                                                    ->helperText('Your agency\'s official website')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('facebook_url')
                                                    ->label('Facebook Page')
                                                    ->url()
                                                    ->placeholder('https://facebook.com/youragency')
                                                    ->helperText('Facebook business page (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('instagram_url')
                                                    ->label('Instagram Profile')
                                                    ->url()
                                                    ->placeholder('https://instagram.com/youragency')
                                                    ->helperText('Instagram business profile (optional)')
                                                    ->columnSpan(1),
                                                    
                                                TextInput::make('linkedin_url')
                                                    ->label('LinkedIn Page')
                                                    ->url()
                                                    ->placeholder('https://linkedin.com/company/youragency')
                                                    ->helperText('LinkedIn business page (optional)')
                                                    ->columnSpan(1),
                                            ]),
                                    ])->collapsible()->collapsed(),
                            ])
                            ->columnSpan(['lg' => 2]),

                        // Sidebar (1/3 width)
                        Group::make()
                            ->schema([
                                // Agency Logo
                                Section::make('Agency Branding')
                                    ->description('Upload your agency logo and branding materials')
                                    ->schema([
                                        FileUpload::make('logo')
                                            ->label('Agency Logo')
                                            ->image()
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '1:1',
                                                '16:9',
                                                '4:3',
                                            ])
                                            ->directory('agency-logos')
                                            ->visibility('public')
                                            ->helperText('Upload your agency logo (JPG, PNG, SVG)'),
                                    ]),

                                // Agency Settings
                                Section::make('Agency Settings')
                                    ->description('Configuration and status settings')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Agency Active')
                                            ->helperText('Toggle agency active status')
                                            ->default(true),
                                            
                                        Toggle::make('is_verified')
                                            ->label('Verified Agency')
                                            ->helperText('Verification status (admin controlled)')
                                            ->disabled()
                                            ->default(false),
                                            
                                        Toggle::make('accepts_new_agents')
                                            ->label('Accepting New Agents')
                                            ->helperText('Whether agency is accepting new agent applications')
                                            ->default(true),
                                    ]),

                                // Statistics (Read-only)
                                Section::make('Agency Statistics')
                                    ->description('Current agency metrics')
                                    ->schema([
                                        TextInput::make('total_agents')
                                            ->label('Total Agents')
                                            ->disabled()
                                            ->helperText('Number of agents in your agency'),
                                            
                                        TextInput::make('total_properties')
                                            ->label('Total Properties')
                                            ->disabled()
                                            ->helperText('Number of properties managed'),
                                            
                                        TextInput::make('active_listings')
                                            ->label('Active Listings')
                                            ->disabled()
                                            ->helperText('Currently active property listings'),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }
}
