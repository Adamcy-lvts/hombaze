<?php

namespace App\Filament\Landlord\Pages\Auth;

use App\Models\PropertyOwner;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    public function getMaxWidth(): ?string
    {
        return '5xl';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                            ->helperText('Your primary contact number')
                            ->columnSpan(1),

                        Placeholder::make('profile_placeholder')
                            ->columnSpan(1),

                        $this->getPasswordFormComponent()
                            ->columnSpan(1),

                        $this->getPasswordConfirmationFormComponent()
                            ->columnSpan(1),
                    ]),

                Section::make('Property Owner Details')
                    ->description('Update your landlord profile information')
                    ->columns(2)
                    ->schema([
                        Select::make('propertyOwnerProfile.type')
                            ->label('Owner Type')
                            ->options(PropertyOwner::getTypes())
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        TextInput::make('propertyOwnerProfile.company_name')
                            ->label('Company/Organization Name')
                            ->maxLength(255)
                            ->visible(fn ($get) => in_array($get('propertyOwnerProfile.type'), ['company', 'trust', 'government'], true))
                            ->required(fn ($get) => in_array($get('propertyOwnerProfile.type'), ['company', 'trust', 'government'], true))
                            ->columnSpan(1),

                        TextInput::make('propertyOwnerProfile.first_name')
                            ->label('First Name')
                            ->maxLength(255)
                            ->required(fn ($get) => ($get('propertyOwnerProfile.type') ?? PropertyOwner::TYPE_INDIVIDUAL) === PropertyOwner::TYPE_INDIVIDUAL)
                            ->visible(fn ($get) => ($get('propertyOwnerProfile.type') ?? PropertyOwner::TYPE_INDIVIDUAL) === PropertyOwner::TYPE_INDIVIDUAL)
                            ->columnSpan(1),

                        TextInput::make('propertyOwnerProfile.last_name')
                            ->label('Last Name')
                            ->maxLength(255)
                            ->required(fn ($get) => ($get('propertyOwnerProfile.type') ?? PropertyOwner::TYPE_INDIVIDUAL) === PropertyOwner::TYPE_INDIVIDUAL)
                            ->visible(fn ($get) => ($get('propertyOwnerProfile.type') ?? PropertyOwner::TYPE_INDIVIDUAL) === PropertyOwner::TYPE_INDIVIDUAL)
                            ->columnSpan(1),

                        TextInput::make('propertyOwnerProfile.tax_id')
                            ->label('Tax ID/BVN')
                            ->maxLength(50)
                            ->columnSpan(1),

                        Select::make('propertyOwnerProfile.preferred_communication')
                            ->label('Preferred Communication')
                            ->options([
                                'email' => 'Email',
                                'phone' => 'Phone Call',
                                'sms' => 'SMS/Text Message',
                                'whatsapp' => 'WhatsApp',
                            ])
                            ->columnSpan(1),

                        Textarea::make('propertyOwnerProfile.address')
                            ->label('Street Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Select::make('propertyOwnerProfile.state_id')
                            ->label('State')
                            ->options(State::pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('propertyOwnerProfile.city_id', null);
                                $set('propertyOwnerProfile.area_id', null);
                            })
                            ->columnSpan(1),

                        Select::make('propertyOwnerProfile.city_id')
                            ->label('City')
                            ->options(fn ($get) => $get('propertyOwnerProfile.state_id')
                                ? City::where('state_id', $get('propertyOwnerProfile.state_id'))->pluck('name', 'id')
                                : []
                            )
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('propertyOwnerProfile.area_id', null))
                            ->columnSpan(1),

                        Select::make('propertyOwnerProfile.area_id')
                            ->label('Area/Neighborhood')
                            ->options(fn ($get) => $get('propertyOwnerProfile.city_id')
                                ? Area::where('city_id', $get('propertyOwnerProfile.city_id'))->pluck('name', 'id')
                                : []
                            )
                            ->searchable()
                            ->columnSpan(1),
                    ]),

                Section::make('Profile Photo')
                    ->description('Upload your profile photo')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('propertyOwnerProfile.profile_photo')
                            ->label('Profile Photo')
                            ->image()
                            ->maxSize(2048)
                            ->imageEditor()
                            ->directory('property-owners/profile-photos')
                            ->visibility('private')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (!empty($data['password'])) {
            $userData['password'] = bcrypt($data['password']);
        }

        $record->update($userData);

        $ownerData = $data['propertyOwnerProfile'] ?? [];

        $profilePhoto = $ownerData['profile_photo'] ?? null;
        if (is_array($profilePhoto)) {
            $profilePhoto = $profilePhoto[0] ?? null;
        }
        if ($profilePhoto !== null) {
            $ownerData['profile_photo'] = $profilePhoto;
        }

        if (array_key_exists('phone', $userData)) {
            $ownerData['phone'] = $userData['phone'];
        }
        if (array_key_exists('email', $userData)) {
            $ownerData['email'] = $userData['email'];
        }

        $record->propertyOwnerProfile()->updateOrCreate(
            ['user_id' => $record->id],
            $ownerData
        );

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();

        return $record;
    }

    protected function fillForm(): void
    {
        $user = auth()->user();
        $owner = $user->propertyOwnerProfile;

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        if ($owner) {
            $data['propertyOwnerProfile'] = [
                'type' => $owner->type ?? PropertyOwner::TYPE_INDIVIDUAL,
                'first_name' => $owner->first_name,
                'last_name' => $owner->last_name,
                'company_name' => $owner->company_name,
                'tax_id' => $owner->tax_id,
                'address' => $owner->address,
                'state_id' => $owner->state_id,
                'city_id' => $owner->city_id,
                'area_id' => $owner->area_id,
                'preferred_communication' => $owner->preferred_communication,
                'profile_photo' => $owner->profile_photo ? [$owner->profile_photo] : [],
            ];
        } else {
            $data['propertyOwnerProfile'] = [
                'type' => PropertyOwner::TYPE_INDIVIDUAL,
            ];
        }

        $this->form->fill($data);
    }
}
