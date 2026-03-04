<?php

namespace App\Filament\Agency\Pages;

use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use App\Models\Agency;
use App\Models\Agent;
use App\Models\State;
use App\Models\City;
use App\Models\Area;

class ProfileCompletion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.agency.pages.profile-completion';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Complete Your Agency Profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $agency = $user->ownedAgencies()->first();

        // Bind model to form to support relationships and media
        $this->form->model($agency ?? Agency::class);

        if ($agency) {
            $address = $agency->address ?? [];
            $social = $agency->social_media ?? [];
            $specializations = $agency->specializations ? explode(',', $agency->specializations) : [];

            // Get Agent (Owner) info if exists
            $agent = Agent::where('user_id', $user->id)->first();

            $this->form->fill([
                'agency_name' => $agency->name,
                'email' => $agency->email,
                'phone' => $agency->phone,
                'website' => $agency->website,
                'street_address' => $address['street'] ?? '',
                'state_id' => $agency->state_id,
                'city_id' => $agency->city_id,
                'area_id' => $agency->area_id,
                'license_number' => $agency->license_number,
                'license_expiry_date' => $agency->license_expiry_date,
                'years_in_business' => $agency->years_in_business,
                'specializations' => $specializations,
                'description' => $agency->description,
                'social_facebook' => $social['facebook'] ?? null,
                'social_twitter' => $social['twitter'] ?? null,
                'social_instagram' => $social['instagram'] ?? null,
                'social_linkedin' => $social['linkedin'] ?? null,
                
                // Agent fields
                'nin_number' => $agent->nin_number ?? '',
            ]);
        } else {
             $this->form->fill();
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Basic Info')
                        ->description('Essential agency details')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Section::make('Identity')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('logo')
                                        ->collection('logo')
                                        ->avatar()
                                        ->image()
                                        ->imageEditor()
                                        ->circleCropper()
                                        ->helperText('Upload your agency logo'),

                                    TextInput::make('agency_name')
                                        ->label('Agency Name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->unique('agencies', 'email', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                            return $rule->ignore(Auth::user()->ownedAgencies()->first()->id);
                                        })
                                        ->maxLength(255),

                                    TextInput::make('phone')
                                        ->tel()
                                        ->required()
                                        ->maxLength(20),

                                    TextInput::make('website')
                                        ->url()
                                        ->prefix('https://')
                                        ->maxLength(255)
                                        ->placeholder('www.example.com'),
                                ])->columns(2),
                        ]),

                    Step::make('Location')
                        ->description('Where can clients find you?')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Section::make('Address Details')
                                ->schema([
                                    Select::make('state_id')
                                        ->label('State')
                                        ->options(State::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),

                                    Select::make('city_id')
                                        ->label('City')
                                        ->options(fn (Get $get) => City::where('state_id', $get('state_id'))->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->disabled(fn (Get $get) => blank($get('state_id')))
                                        ->afterStateUpdated(fn (Set $set) => $set('area_id', null)),

                                    Select::make('area_id')
                                        ->label('Area')
                                        ->options(fn (Get $get) => Area::where('city_id', $get('city_id'))->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->disabled(fn (Get $get) => blank($get('city_id'))),

                                    Textarea::make('street_address')
                                        ->label('Street Address')
                                        ->required()
                                        ->maxLength(500)
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    Step::make('Professional Info')
                        ->description('Credentials and expertise')
                        ->icon('heroicon-o-briefcase')
                        ->schema([
                            Section::make('Details')
                                ->schema([
                                    TextInput::make('license_number')
                                        ->label('License Number')
                                        ->maxLength(255)
                                        ->placeholder('e.g. RC123456'),

                                    DatePicker::make('license_expiry_date')
                                        ->label('License Expiry'),

                                    TextInput::make('years_in_business')
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0),

                                    TagsInput::make('specializations')
                                        ->suggestions(['Residential', 'Commercial', 'Land', 'Property Management', 'Shortlets'])
                                        ->separator(','),

                                    Textarea::make('description')
                                        ->label('About Agency')
                                        ->required()
                                        ->rows(4)
                                        ->maxLength(1000)
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    Step::make('Social Media')
                        ->description('Connect with your audience')
                        ->icon('heroicon-o-share')
                        ->schema([
                            Section::make('Social Handles')
                                ->schema([
                                    TextInput::make('social_facebook')
                                        ->label('Facebook')
                                        ->prefix('facebook.com/'),
                                    TextInput::make('social_twitter')
                                        ->label('Twitter / X')
                                        ->prefix('x.com/'),
                                    TextInput::make('social_instagram')
                                        ->label('Instagram')
                                        ->prefix('instagram.com/'),
                                    TextInput::make('social_linkedin')
                                        ->label('LinkedIn')
                                        ->prefix('linkedin.com/in/'),
                                ])->columns(2),
                        ]),

                    Step::make('Verification')
                        ->description('Verify your identity')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Section::make('Agency Verification')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('certificate')
                                        ->label('Agency Certificate')
                                        ->collection('certificate')
                                        ->required()
                                        ->helperText('Upload your agency incorporation certificate'),
                                ]),
                            Section::make('Owner Verification')
                                ->schema([
                                    TextInput::make('nin_number')
                                        ->label('National ID Number (NIN)')
                                        ->required()
                                        ->maxLength(255),
                                    FileUpload::make('nin_document')
                                        ->label('NIN ID Card')
                                        ->required()
                                        ->disk('public')
                                        ->directory('temp-nin')
                                        ->visibility('private')
                                        ->helperText('Upload a clear picture of your NIN ID card'),
                                ]),
                        ]),
                ])
                ->submitAction(
                    Action::make('submit')
                        ->label('Complete Profile')
                        ->action('save')
                ),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        \Illuminate\Support\Facades\Log::info('ProfileCompletion: Save method called.');
        try {
            $data = $this->form->getState();
            \Illuminate\Support\Facades\Log::info('ProfileCompletion: Form state retrieved.', ['data' => $data]);
            
            $user = Auth::user();
            $agency = $user->ownedAgencies()->first();

            if ($agency) {
                // Prepare JSON fields
                $addressData = ['street' => $data['street_address']];
                
                $socialMedia = array_filter([
                    'facebook' => $data['social_facebook'] ?? null,
                    'twitter' => $data['social_twitter'] ?? null,
                    'instagram' => $data['social_instagram'] ?? null,
                    'linkedin' => $data['social_linkedin'] ?? null,
                ]);

                // Prepare specializations string
                $specializations = is_array($data['specializations']) ? implode(',', $data['specializations']) : $data['specializations'];

                // Update Agency
                $agency->update([
                    'name' => $data['agency_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'website' => $data['website'],
                    'address' => $addressData,
                    'state_id' => $data['state_id'],
                    'city_id' => $data['city_id'],
                    'area_id' => $data['area_id'],
                    'license_number' => $data['license_number'],
                    'license_expiry_date' => $data['license_expiry_date'],
                    'years_in_business' => $data['years_in_business'],
                    'specializations' => $specializations,
                    'description' => $data['description'],
                    'social_media' => !empty($socialMedia) ? $socialMedia : null,
                ]);

                // Update Agency Relations (Media)
                $this->form->model($agency)->saveRelationships();

                // Handle Agent (Owner) Logic
                $agent = Agent::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'agency_id' => $agency->id,
                        'is_verified' => false,
                        'is_active' => true,
                    ]
                );

                // Update Agent NIN
                $agent->update([
                    'nin_number' => $data['nin_number'],
                    'agency_id' => $agency->id, // Ensure linked
                ]);

                // Handle NIN Document Upload
                if (!empty($data['nin_document'])) {
                    // FileUpload returns path relative to disk (public)
                    // We need checking if it's an array (multiple) or string (single)
                    $filePaths = is_array($data['nin_document']) ? $data['nin_document'] : [$data['nin_document']];
                    
                    foreach ($filePaths as $filePath) {
                        try {
                            $absolutePath = storage_path('app/public/' . $filePath);
                            if (file_exists($absolutePath)) {
                                $agent->addMedia($absolutePath)
                                      ->toMediaCollection('id_document');
                            }
                        } catch (\Exception $e) {
                            // Log or handle silent failure
                        }
                    }
                }

                // Mark profile as completed
                $user->update(['profile_completed' => true]);
                
                Notification::make() 
                    ->title('Profile Completed')
                    ->success()
                    ->send();

                $this->redirect(route('filament.agency.pages.agency-dashboard', ['tenant' => $agency]));
            }

        } catch (Halt $exception) {
            \Illuminate\Support\Facades\Log::warning('ProfileCompletion: Validation halted.', ['message' => $exception->getMessage()]);
            return;
        } catch (\Exception $exception) {
            \Illuminate\Support\Facades\Log::error('ProfileCompletion: Error saving profile.', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            Notification::make()
                ->title('Error saving profile')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
