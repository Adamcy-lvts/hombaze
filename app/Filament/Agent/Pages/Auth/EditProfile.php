<?php

namespace App\Filament\Agent\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use App\Models\State;
use App\Models\City;
use App\Models\Area;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Account Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ]),
                    ]),
                
                Section::make('Agent Profile')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('agent.phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),
                                
                                TextInput::make('agent.license_number')
                                    ->label('License Number')
                                    ->maxLength(100),
                            ]),
                        
                        Textarea::make('agent.bio')
                            ->label('Professional Bio')
                            ->maxLength(1000)
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('agent.years_experience')
                                    ->label('Years of Experience')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(50),
                                
                                Select::make('agent.specializations')
                                    ->label('Specializations')
                                    ->multiple()
                                    ->options([
                                        'residential_sales' => 'Residential Sales',
                                        'residential_rentals' => 'Residential Rentals',
                                        'commercial_sales' => 'Commercial Sales',
                                        'commercial_rentals' => 'Commercial Rentals',
                                        'land_sales' => 'Land Sales',
                                        'luxury_properties' => 'Luxury Properties',
                                        'affordable_housing' => 'Affordable Housing',
                                        'student_housing' => 'Student Housing',
                                    ]),
                            ]),
                    ]),
                
                Section::make('Location & Coverage')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('agent.state_id')
                                    ->label('Primary State')
                                    ->options(State::pluck('name', 'id'))
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('agent.city_id', null);
                                        $set('agent.area_id', null);
                                    }),
                                
                                Select::make('agent.city_id')
                                    ->label('Primary City')
                                    ->options(function (callable $get) {
                                        $stateId = $get('agent.state_id');
                                        if (!$stateId) return [];
                                        return City::where('state_id', $stateId)->pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set) => $set('agent.area_id', null)),
                                
                                Select::make('agent.area_id')
                                    ->label('Primary Area')
                                    ->options(function (callable $get) {
                                        $cityId = $get('agent.city_id');
                                        if (!$cityId) return [];
                                        return Area::where('city_id', $cityId)->pluck('name', 'id');
                                    }),
                            ]),
                        
                        TextInput::make('agent.office_address')
                            ->label('Office Address')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Professional Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('agent.website')
                                    ->label('Website')
                                    ->url()
                                    ->maxLength(255),
                                
                                TextInput::make('agent.social_media_links')
                                    ->label('Social Media Profile')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('LinkedIn, Facebook, etc.'),
                            ]),
                        
                        FileUpload::make('agent.profile_photo')
                            ->label('Profile Photo')
                            ->image()
                            ->maxSize(2048)
                            ->directory('agent-profiles')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
    
    /**
     * Fill the form with current data
     */
    protected function fillForm(): void
    {
        $user = auth()->user();
        $agent = $user->agent;
        
        $data = [
            'name' => $user->name,
            'email' => $user->email,
        ];
        
        if ($agent) {
            $data['agent'] = [
                'phone' => $agent->phone,
                'license_number' => $agent->license_number,
                'bio' => $agent->bio,
                'years_experience' => $agent->years_experience,
                'specializations' => $agent->specializations,
                'state_id' => $agent->state_id,
                'city_id' => $agent->city_id,
                'area_id' => $agent->area_id,
                'office_address' => $agent->office_address,
                'website' => $agent->website,
                'social_media_links' => $agent->social_media_links,
                'profile_photo' => $agent->profile_photo,
            ];
        }
        
        $this->form->fill($data);
    }
}
