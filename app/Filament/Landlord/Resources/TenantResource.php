<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\TenantResource\Pages;
use App\Filament\Landlord\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Tenants';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_of_birth'),

                                Forms\Components\TextInput::make('nationality')
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Employment Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('employment_status')
                                    ->options([
                                        'employed' => 'Employed',
                                        'self_employed' => 'Self Employed',
                                        'unemployed' => 'Unemployed',
                                        'retired' => 'Retired',
                                        'student' => 'Student',
                                    ]),

                                Forms\Components\TextInput::make('employer_name')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('occupation')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('monthly_income')
                                    ->numeric()
                                    ->prefix('₦'),
                            ]),
                    ]),

                Forms\Components\Section::make('Identification & Emergency Contact')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('identification_type')
                                    ->options([
                                        'national_id' => 'National ID',
                                        'international_passport' => 'International Passport',
                                        'drivers_license' => 'Driver\'s License',
                                        'voters_card' => 'Voter\'s Card',
                                    ]),

                                Forms\Components\TextInput::make('identification_number')
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('emergency_contact_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('emergency_contact_phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Guarantor Information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('guarantor_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('guarantor_phone')
                                    ->tel()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('guarantor_email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('employment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'employed' => 'success',
                        'self_employed' => 'info',
                        'unemployed' => 'danger',
                        'retired' => 'gray',
                        'student' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('monthly_income')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_status')
                    ->options([
                        'employed' => 'Employed',
                        'self_employed' => 'Self Employed',
                        'unemployed' => 'Unemployed',
                        'retired' => 'Retired',
                        'student' => 'Student',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('landlord_id', Auth::id());
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
