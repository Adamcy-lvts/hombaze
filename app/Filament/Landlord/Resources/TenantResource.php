<?php

namespace App\Filament\Landlord\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Landlord\Resources\TenantResource\Pages\ListTenants;
use App\Filament\Landlord\Resources\TenantResource\Pages\CreateTenant;
use App\Filament\Landlord\Resources\TenantResource\Pages\EditTenant;
use App\Filament\Landlord\Resources\TenantResource\Pages;
use App\Filament\Landlord\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Tenants';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('date_of_birth'),

                                TextInput::make('nationality')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Employment Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('employment_status')
                                    ->options([
                                        'employed' => 'Employed',
                                        'self_employed' => 'Self Employed',
                                        'unemployed' => 'Unemployed',
                                        'retired' => 'Retired',
                                        'student' => 'Student',
                                    ]),

                                TextInput::make('employer_name')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('occupation')
                                    ->maxLength(255),

                                TextInput::make('monthly_income')
                                    ->numeric()
                                    ->prefix('₦'),
                            ]),
                    ]),

                Section::make('Identification & Emergency Contact')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('identification_type')
                                    ->options([
                                        'national_id' => 'National ID',
                                        'international_passport' => 'International Passport',
                                        'drivers_license' => 'Driver\'s License',
                                        'voters_card' => 'Voter\'s Card',
                                    ]),

                                TextInput::make('identification_number')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('emergency_contact_name')
                                    ->maxLength(255),

                                TextInput::make('emergency_contact_phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Guarantor Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('guarantor_name')
                                    ->maxLength(255),

                                TextInput::make('guarantor_phone')
                                    ->tel()
                                    ->maxLength(255),

                                TextInput::make('guarantor_email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('employment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'employed' => 'success',
                        'self_employed' => 'info',
                        'unemployed' => 'danger',
                        'retired' => 'gray',
                        'student' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('monthly_income')
                    ->money('NGN')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('employment_status')
                    ->options([
                        'employed' => 'Employed',
                        'self_employed' => 'Self Employed',
                        'unemployed' => 'Unemployed',
                        'retired' => 'Retired',
                        'student' => 'Student',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $landlordId = Auth::id();
        \Log::info('[TenantResource] getEloquentQuery called', [
            'auth_user_id' => $landlordId,
            'auth_check' => Auth::check(),
        ]);
        
        return parent::getEloquentQuery()
            ->where('landlord_id', $landlordId);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
