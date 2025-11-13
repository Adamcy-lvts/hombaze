<?php

namespace App\Filament\Landlord\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Section;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\State;
use App\Models\City;
use App\Models\Area;

class ProfileCompletion extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';
    protected string $view = 'filament.landlord.pages.profile-completion';
    protected static ?string $title = 'Complete Your Profile';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $propertyOwner = $user->propertyOwnerProfile;
        
        $this->form->fill([
            'first_name' => $propertyOwner?->first_name ?? '',
            'last_name' => $propertyOwner?->last_name ?? '',
            'phone' => $user->phone,
            'email' => $user->email,
            'type' => $propertyOwner?->type ?? 'individual',
            'company_name' => $propertyOwner?->company_name ?? '',
            'address' => $propertyOwner?->address ?? '',
            'state_id' => $propertyOwner?->state_id ?? null,
            'city_id' => $propertyOwner?->city_id ?? null,
            'area_id' => $propertyOwner?->area_id ?? null,
            'date_of_birth' => $propertyOwner?->date_of_birth ?? null,
            'preferred_communication' => $propertyOwner?->preferred_communication ?? 'email',
            'profile_photo' => $propertyOwner?->profile_photo ? [$propertyOwner->profile_photo] : [],
            'id_document' => $propertyOwner?->id_document ? [$propertyOwner->id_document] : [],
            'proof_of_address' => $propertyOwner?->proof_of_address ? [$propertyOwner->proof_of_address] : [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Personal Information')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            Section::make('Basic Details')
                                ->description('Tell us about yourself')
                                ->schema([
                                    Select::make('type')
                                        ->label('Account Type')
                                        ->required()
                                        ->options([
                                            'individual' => 'Individual Property Owner',
                                            'company' => 'Company/Business',
                                        ])
                                        ->live()
                                        ->afterStateUpdated(fn ($state, callable $set) => 
                                            $state === 'individual' ? $set('company_name', '') : null
                                        ),
                                    
                                    TextInput::make('first_name')
                                        ->label('First Name')
                                        ->required()
                                        ->maxLength(255),
                                    
                                    TextInput::make('last_name')
                                        ->label('Last Name')
                                        ->required()
                                        ->maxLength(255),
                                    
                                    TextInput::make('company_name')
                                        ->label('Company Name')
                                        ->maxLength(255)
                                        ->required(fn ($get) => $get('type') === 'company')
                                        ->visible(fn ($get) => $get('type') === 'company'),
                                    
                                    TextInput::make('phone')
                                        ->label('Phone Number')
                                        ->tel()
                                        ->required()
                                        ->maxLength(20),
                                    
                                    TextInput::make('email')
                                        ->label('Email Address')
                                        ->email()
                                        ->required()
                                        ->disabled(),
                                ]),
                        ]),
                    
                    Step::make('Address Information')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Section::make('Location Details')
                                ->description('Provide your address information')
                                ->schema([
                                    Textarea::make('address')
                                        ->label('Street Address')
                                        ->required()
                                        ->rows(3)
                                        ->placeholder('Enter your full address...'),
                                    
                                    Select::make('state_id')
                                        ->label('State')
                                        ->required()
                                        ->options(State::pluck('name', 'id'))
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function (callable $set) {
                                            $set('city_id', null);
                                            $set('area_id', null);
                                        }),
                                    
                                    Select::make('city_id')
                                        ->label('City')
                                        ->required()
                                        ->options(fn ($get) => $get('state_id') 
                                            ? City::where('state_id', $get('state_id'))->pluck('name', 'id')
                                            : []
                                        )
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(fn (callable $set) => $set('area_id', null)),
                                    
                                    Select::make('area_id')
                                        ->label('Area/Neighborhood')
                                        ->options(fn ($get) => $get('city_id') 
                                            ? Area::where('city_id', $get('city_id'))->pluck('name', 'id')
                                            : []
                                        )
                                        ->searchable(),
                                    
                                    Select::make('preferred_communication')
                                        ->label('Preferred Communication Method')
                                        ->options([
                                            'email' => 'Email',
                                            'phone' => 'Phone Call',
                                            'sms' => 'SMS/Text Message',
                                            'whatsapp' => 'WhatsApp',
                                        ])
                                        ->required(),
                                ]),
                        ]),
                    
                    Step::make('ID Verification')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            Section::make('Identity Verification')
                                ->description('Upload your identification documents')
                                ->schema([
                                    FileUpload::make('id_document')
                                        ->label('Government ID (NIN, Driver\'s License, Passport)')
                                        ->required()
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(5120)
                                        ->directory('property-owners/id-documents')
                                        ->visibility('private')
                                        ->helperText('Upload a clear copy of your government-issued ID (PDF, JPG, PNG - Max 5MB)'),
                                    
                                    FileUpload::make('proof_of_address')
                                        ->label('Proof of Address')
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(5120)
                                        ->directory('property-owners/proof-of-address')
                                        ->visibility('private')
                                        ->helperText('Upload utility bill, bank statement, or similar document (Optional)'),
                                ]),
                        ]),
                    
                    Step::make('Profile Photo')
                        ->icon('heroicon-o-camera')
                        ->schema([
                            Section::make('Profile Photo')
                                ->description('Add a professional profile photo')
                                ->schema([
                                    FileUpload::make('profile_photo')
                                        ->label('Profile Photo')
                                        ->image()
                                        ->required()
                                        ->maxSize(2048)
                                        ->imageEditor()
                                        ->directory('property-owners/profile-photos')
                                        ->visibility('private')
                                        ->helperText('Upload a professional photo (Max 2MB)'),
                                ]),
                        ]),
                ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $propertyOwner = $user->propertyOwnerProfile;

        // Update property owner profile
        $propertyOwner->update([
            'type' => $data['type'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'company_name' => $data['company_name'] ?? null,
            'phone' => $data['phone'],
            'email' => $user->email, // Use user's email since form field is disabled
            'address' => $data['address'],
            'state_id' => $data['state_id'],
            'city_id' => $data['city_id'],
            'area_id' => $data['area_id'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'preferred_communication' => $data['preferred_communication'],
            'profile_photo' => !empty($data['profile_photo']) ? $data['profile_photo'][0] : null,
            'id_document' => !empty($data['id_document']) ? $data['id_document'][0] : null,
            'proof_of_address' => !empty($data['proof_of_address']) ? $data['proof_of_address'][0] : null,
        ]);

        // Update user phone if changed
        if ($user->phone !== $data['phone']) {
            $user->update(['phone' => $data['phone']]);
        }

        // Mark profile completion steps
        $user->markStepCompleted('contact_details');
        $user->markStepCompleted('address');
        
        if (!empty($data['id_document'])) {
            $user->markStepCompleted('id_verification');
        }
        
        if (!empty($data['profile_photo'])) {
            $user->markStepCompleted('profile_photo');
        }

        Notification::make()
            ->title('Profile Updated Successfully!')
            ->body('Your profile has been updated. You can now access all features.')
            ->success()
            ->send();

        // Redirect to dashboard
        $this->redirect(route('filament.landlord.pages.dashboard'));
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Complete Profile')
                ->action('save')
                ->color('primary'),
        ];
    }
}
