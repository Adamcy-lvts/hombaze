<?php

namespace App\Filament\Agent\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ProfileCompletion extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.agent.pages.profile-completion';
    protected static ?string $title = 'Complete Your Profile';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $agent = $user->agentProfile;
        
        $this->form->fill([
            'bio' => $agent?->bio ?? '',
            'years_experience' => $agent?->years_experience ?? 0,
            'specializations' => $agent?->specializations ? explode(',', $agent->specializations) : [],
            'service_areas' => $agent?->service_areas ? json_decode($agent->service_areas, true) : [],
            'languages' => $agent?->languages ? json_decode($agent->languages, true) : ['english'],
            'certifications' => [],
            'phone' => $user->phone,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Professional Details')
                        ->icon('heroicon-o-briefcase')
                        ->schema([
                            Section::make('Professional Information')
                                ->description('Tell us about your professional background')
                                ->schema([
                                    Textarea::make('bio')
                                        ->label('Professional Bio')
                                        ->required()
                                        ->rows(4)
                                        ->placeholder('Describe your experience and expertise in real estate...'),
                                    
                                    TextInput::make('years_experience')
                                        ->label('Years of Experience')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->maxValue(50),
                                    
                                    CheckboxList::make('specializations')
                                        ->label('Specializations')
                                        ->required()
                                        ->options([
                                            'residential_sales' => 'Residential Sales',
                                            'residential_rentals' => 'Residential Rentals',
                                            'commercial_sales' => 'Commercial Sales',
                                            'commercial_rentals' => 'Commercial Rentals',
                                            'land_sales' => 'Land Sales',
                                            'luxury_properties' => 'Luxury Properties',
                                            'investment_properties' => 'Investment Properties',
                                        ])
                                        ->columns(2),
                                ]),
                        ]),
                    
                    Wizard\Step::make('Service Areas')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Section::make('Service Areas')
                                ->description('Define the areas where you provide services')
                                ->schema([
                                    Select::make('service_areas')
                                        ->label('Service Areas')
                                        ->multiple()
                                        ->required()
                                        ->options(\App\Models\Area::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload(),
                                    
                                    CheckboxList::make('languages')
                                        ->label('Languages Spoken')
                                        ->required()
                                        ->options([
                                            'english' => 'English',
                                            'yoruba' => 'Yoruba',
                                            'igbo' => 'Igbo',
                                            'hausa' => 'Hausa',
                                            'french' => 'French',
                                            'arabic' => 'Arabic',
                                        ])
                                        ->columns(3),
                                ]),
                        ]),
                    
                    Wizard\Step::make('Certifications')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Section::make('Professional Certifications')
                                ->description('Upload your professional certifications and documents')
                                ->schema([
                                    FileUpload::make('certifications')
                                        ->label('Certifications & Licenses')
                                        ->multiple()
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(5120)
                                        ->helperText('Upload your real estate license, certifications, etc. (PDF, JPG, PNG - Max 5MB each)'),
                                ]),
                        ]),
                    
                    Wizard\Step::make('Profile Photo')
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
                                        ->helperText('Upload a professional headshot (Max 2MB)'),
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
        $agent = $user->agentProfile;

        // Update agent profile
        $agent->update([
            'bio' => $data['bio'],
            'years_experience' => $data['years_experience'],
            'specializations' => implode(',', $data['specializations']),
            'service_areas' => json_encode($data['service_areas']),
            'languages' => json_encode($data['languages']),
            'is_verified' => false, // Will be verified by admin
        ]);

        // Mark profile completion steps
        $user->markStepCompleted('professional_details');
        $user->markStepCompleted('service_areas');
        
        if (!empty($data['certifications'])) {
            $user->markStepCompleted('certifications');
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
        $this->redirect(route('filament.agent.pages.dashboard'));
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
