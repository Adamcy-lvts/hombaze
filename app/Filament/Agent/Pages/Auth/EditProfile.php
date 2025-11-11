<?php

namespace App\Filament\Agent\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends BaseEditProfile
{
    public function getMaxWidth(): ?string
    {
        return '5xl';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Account Information')
                    ->description('Update your basic account details')
                    ->columns(2)
                    ->schema([
                        $this->getNameFormComponent()
                            ->required()
                            ->columnSpan(1),

                        $this->getEmailFormComponent()
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->helperText('Your contact phone number for clients')
                            ->columnSpan(1),

                        // Empty column to balance layout
                        \Filament\Forms\Components\Placeholder::make('')
                            ->columnSpan(1),

                        $this->getPasswordFormComponent()
                            ->columnSpan(1),

                        $this->getPasswordConfirmationFormComponent()
                            ->columnSpan(1),
                    ]),

                Section::make('Professional Details')
                    ->description('Your real estate professional information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('agentProfile.license_number')
                            ->label('License Number')
                            ->maxLength(100)
                            ->helperText('Your real estate license number')
                            ->columnSpan(1),

                        TextInput::make('agentProfile.years_experience')
                            ->label('Years of Experience')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50)
                            ->columnSpan(1),

                        Textarea::make('agentProfile.bio')
                            ->label('Professional Bio')
                            ->maxLength(1000)
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Tell clients about your background and expertise'),

                        TagsInput::make('specializations_list')
                            ->label('Specializations')
                            ->placeholder('Add specializations (e.g., Residential Sales)')
                            ->helperText('Press Enter to add each specialization')
                            ->columnSpanFull(),

                        TagsInput::make('languages_list')
                            ->label('Languages Spoken')
                            ->placeholder('Add languages (e.g., English, French)')
                            ->helperText('Press Enter to add each language')
                            ->columnSpanFull(),
                    ]),

                Section::make('Profile Photo')
                    ->description('Upload your professional photo')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Profile Photo')
                            ->image()
                            ->maxSize(2048)
                            ->directory('avatars')
                            ->imageEditor()
                            ->circleCropper()
                            ->alignCenter()
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update user fields
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (!empty($data['password'])) {
            $userData['password'] = bcrypt($data['password']);
        }

        if (isset($data['avatar'])) {
            $userData['avatar'] = $data['avatar'];
        }

        $record->update($userData);

        // Update or create agent profile
        $agentData = [];

        if (isset($data['agentProfile']['license_number'])) {
            $agentData['license_number'] = $data['agentProfile']['license_number'];
        }

        if (isset($data['agentProfile']['years_experience'])) {
            $agentData['years_experience'] = $data['agentProfile']['years_experience'];
        }

        if (isset($data['agentProfile']['bio'])) {
            $agentData['bio'] = $data['agentProfile']['bio'];
        }

        // Handle specializations
        if (isset($data['specializations_list'])) {
            $agentData['specializations'] = implode(',', $data['specializations_list']);
        }

        // Handle languages
        if (isset($data['languages_list'])) {
            $agentData['languages'] = $data['languages_list'];
        }

        if (!empty($agentData)) {
            $record->agentProfile()->updateOrCreate(
                ['user_id' => $record->id],
                $agentData
            );
        }

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();

        return $record;
    }

    protected function fillForm(): void
    {
        $user = auth()->user();
        $agent = $user->agentProfile;

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
        ];

        if ($agent) {
            $data['agentProfile'] = [
                'license_number' => $agent->license_number,
                'years_experience' => $agent->years_experience,
                'bio' => $agent->bio,
            ];

            // Handle specializations
            if ($agent->specializations) {
                $data['specializations_list'] = explode(',', $agent->specializations);
            }

            // Handle languages
            if ($agent->languages) {
                $data['languages_list'] = $agent->languages;
            }
        }

        $this->form->fill($data);
    }
}
