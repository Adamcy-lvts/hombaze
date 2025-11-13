<?php

namespace App\Filament\Agent\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Section;
use App\Models\Area;
use Exception;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileCompletion extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';
    protected string $view = 'filament.agent.pages.profile-completion';
    protected static ?string $title = 'Complete Your Profile';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public $record;

    public function mount(): void
    {
        $user = Auth::user();
        $agent = $user->agentProfile;

        // Set the record for SpatieMediaLibraryFileUpload to work
        $this->record = $agent;

        $this->form->fill([
            'bio' => $agent?->bio ?? '',
            'years_experience' => $agent?->years_experience ?? 0,
            'specializations' => $agent?->specializations ? explode(',', $agent->specializations) : [],
            'service_areas' => $agent?->service_areas ? json_decode($agent->service_areas, true) : [],
            'languages' => $agent?->languages ? json_decode($agent->languages, true) : ['english'],
            'profile_photo' => $user->avatar,
            'phone' => $user->phone,
            'email' => $user->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record)
            ->components([
                Wizard::make([
                    Step::make('Professional Details')
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
                    
                    Step::make('Service Areas')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Section::make('Service Areas')
                                ->description('Define the areas where you provide services')
                                ->schema([
                                    Select::make('service_areas')
                                        ->label('Service Areas')
                                        ->multiple()
                                        ->required()
                                        ->options(Area::pluck('name', 'id'))
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
                    
                    Step::make('Certifications')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Section::make('Professional Certifications')
                                ->description('Upload your professional certifications and documents')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('certifications')
                                        ->label('Certifications & Licenses')
                                        ->multiple()
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                        ->maxSize(5120)
                                        ->collection('certifications')
                                        ->helperText('Upload your real estate license, certifications, etc. (PDF, JPG, PNG - Max 5MB each)'),
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
                                        ->directory('avatars')
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

        try {
            // Log the form data for debugging
            Log::info('ProfileCompletion save data:', $data);

            // Update the agent record with form data first
            $agent->update([
                'bio' => $data['bio'] ?? '',
                'years_experience' => $data['years_experience'] ?? 0,
                'specializations' => !empty($data['specializations']) ? implode(',', $data['specializations']) : '',
                'service_areas' => !empty($data['service_areas']) ? json_encode($data['service_areas']) : '[]',
                'languages' => !empty($data['languages']) ? json_encode($data['languages']) : '["english"]',
                'is_verified' => false, // Will be verified by admin
            ]);

            Log::info('Agent updated successfully', ['agent_id' => $agent->id]);

            // Handle profile photo upload
            if (!empty($data['profile_photo'])) {
                // Save to user's avatar field
                $user->update([
                    'avatar' => $data['profile_photo']
                ]);
                $user->markStepCompleted('profile_photo');
            }

            // Check completion steps
            $user->markStepCompleted('professional_details');
            $user->markStepCompleted('service_areas');
            Log::info('Marked professional_details and service_areas as completed');

            // Check if certifications were uploaded (handled automatically by SpatieMediaLibraryFileUpload)
            if ($agent->fresh()->hasCertifications()) {
                $user->markStepCompleted('certifications');
                Log::info('Marked certifications as completed');
            } else {
                Log::info('No certifications found, step not marked');
            }

            Notification::make()
                ->title('Profile Updated Successfully!')
                ->body('Your profile has been updated. You can now access all features.')
                ->success()
                ->send();

            // Redirect to dashboard
            $this->redirect(route('filament.agent.pages.dashboard'));

        } catch (Exception $e) {
            Notification::make()
                ->title('Error updating profile')
                ->body('There was an error updating your profile: ' . $e->getMessage())
                ->danger()
                ->send();
        }
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
