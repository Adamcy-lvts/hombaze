<?php

namespace App\Filament\Agent\Resources\PropertyResource\Pages;

use App\Filament\Agent\Resources\PropertyResource;
use App\Models\Area;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertySubtype;
use App\Models\PropertyType;
use App\Models\State;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Actions\Action::make('quick_create')
                ->label('Quick Create Draft')
                ->icon('heroicon-o-bolt')
                ->modalHeading('Quick Create Draft')
                ->modalSubmitActionLabel('Create Draft')
                ->form($this->getQuickCreateForm())
                ->action(function (array $data) {
                    $property = Property::create($this->prepareQuickCreateData($data));
                    return redirect()->to($this->getResource()::getUrl('edit', ['record' => $property]));
                }),
        ];
    }

    private function getQuickCreateForm(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            Select::make('listing_type')
                ->options([
                    'sale' => 'For Sale',
                    'rent' => 'For Rent',
                    'lease' => 'For Lease',
                    'shortlet' => 'Shortlet',
                ])
                ->required()
                ->live(),
            TextInput::make('price')
                ->numeric()
                ->prefix('₦')
                ->required(),
            Select::make('price_period')
                ->label('Price Period')
                ->options([
                    'per_month' => 'Per Month',
                    'per_year' => 'Per Year',
                    'per_night' => 'Per Night',
                    'total' => 'Total',
                ])
                ->visible(fn (Get $get): bool => in_array($get('listing_type'), ['rent', 'lease', 'shortlet'], true)),
            Select::make('property_type_id')
                ->label('Property Type')
                ->options(fn (): array => PropertyType::query()->orderBy('name')->pluck('name', 'id')->all())
                ->required()
                ->searchable()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('property_subtype_id', null)),
            Select::make('property_subtype_id')
                ->label('Property Subtype')
                ->options(fn (Get $get): array => PropertySubtype::query()
                    ->where('property_type_id', $get('property_type_id'))
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->required()
                ->searchable(),
            Select::make('state_id')
                ->label('State')
                ->options(fn (): array => State::query()->orderBy('name')->pluck('name', 'id')->all())
                ->required()
                ->searchable()
                ->live()
                ->afterStateUpdated(function (Set $set) {
                    $set('city_id', null);
                    $set('area_id', null);
                }),
            Select::make('city_id')
                ->label('City')
                ->options(fn (Get $get): array => City::query()
                    ->where('state_id', $get('state_id'))
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->required()
                ->searchable()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('area_id', null)),
            Select::make('area_id')
                ->label('Area')
                ->options(fn (Get $get): array => Area::query()
                    ->where('city_id', $get('city_id'))
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable(),
            Select::make('owner_id')
                ->label('Property Owner')
                ->relationship('owner', 'name', function ($query) {
                    $agentProfile = auth()->user()?->agentProfile;
                    return $query->where('agent_id', $agentProfile?->id);
                })
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                ->searchable(['first_name', 'last_name', 'company_name', 'email'])
                ->preload()
                ->required(),
            Textarea::make('address')
                ->label('Street Address')
                ->required()
                ->rows(3),
            Textarea::make('description')
                ->label('Short Description')
                ->rows(3),
        ];
    }

    private function prepareQuickCreateData(array $data): array
    {
        $data['description'] = trim($data['description'] ?? '');
        if ($data['description'] === '') {
            $data['description'] = 'Draft listing. Update details.';
        }

        $data['status'] = $data['status'] ?? 'available';
        $data['is_published'] = false;
        $data['is_verified'] = false;
        $data['is_featured'] = false;
        $data['bedrooms'] = $data['bedrooms'] ?? 0;
        $data['bathrooms'] = $data['bathrooms'] ?? 0;
        $data['furnishing_status'] = $data['furnishing_status'] ?? 'unfurnished';

        $agentProfile = auth()->user()?->agentProfile;
        if (!$agentProfile) {
            throw new \RuntimeException('Agent profile not found.');
        }

        $data['agent_id'] = $agentProfile->id;
        $data['agency_id'] = null;

        return Property::applyListingPackageData($data);
    }
}
