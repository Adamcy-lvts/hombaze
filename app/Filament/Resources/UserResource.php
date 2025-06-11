<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Profile Avatar Section (Top)
                Forms\Components\Section::make('Profile Picture')
                    ->description('Upload and manage user profile picture')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->image()
                            ->directory('user-avatars')
                            ->maxSize(5120)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300')
                            ->helperText('Max 5MB, JPG/PNG only. Recommended size: 300x300px')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                // Main Content Grid
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'lg' => 3,
                ])
                    ->schema([
                        // Left Column - User Information & Security
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('User Information')
                                    ->description('Basic user account information and credentials')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Forms\Components\TextInput::make('phone')
                                                    ->tel()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Forms\Components\Select::make('user_type')
                                                    ->required()
                                                    ->options([
                                                        'super_admin' => 'Super Admin',
                                                        'agency_owner' => 'Agency Owner',
                                                        'agent' => 'Agent',
                                                        'property_owner' => 'Property Owner',
                                                        'tenant' => 'Tenant',
                                                    ])
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Security')
                                    ->description('Account security and authentication settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->maxLength(255)
                                            ->dehydrated(fn($state) => filled($state))
                                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                            ->helperText('Leave blank to keep current password when editing')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'sm' => 1,
                                'lg' => 2,
                            ]),

                        // Right Column - Status & Verification
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Account Status')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 1,
                                        ])
                                            ->schema([
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Account Active')
                                                    ->helperText('Active accounts can log in')
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Forms\Components\Toggle::make('is_verified')
                                                    ->label('Email Verified')
                                                    ->helperText('Email address confirmed')
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Verification Dates')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                            'sm' => 1,
                                        ])
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('email_verified_at')
                                                    ->label('Email Verified At')
                                                    ->disabled()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Forms\Components\DateTimePicker::make('phone_verified_at')
                                                    ->label('Phone Verified At')
                                                    ->disabled()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Forms\Components\DateTimePicker::make('last_login_at')
                                                    ->label('Last Login')
                                                    ->disabled()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'sm' => 1,
                                'lg' => 1,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('user_type')
                    ->badge()
                    ->colors([
                        'danger' => 'super_admin',
                        'success' => 'agency_owner',
                        'warning' => 'agent',
                        'info' => 'property_owner',
                        'primary' => 'tenant',
                    ])
                    ->sortable(),

                // which agency the user belongs to
                Tables\Columns\TextColumn::make('agencies.name')
                    ->label('Agency'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Email Verified')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Login')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Email Verified At')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Phone Verified At')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'agency_owner' => 'Agency Owner',
                        'agent' => 'Agent',
                        'property_owner' => 'Property Owner',
                        'tenant' => 'Tenant',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All users')
                    ->trueLabel('Active users')
                    ->falseLabel('Inactive users'),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Email Verification')
                    ->placeholder('All users')
                    ->trueLabel('Verified users')
                    ->falseLabel('Unverified users'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('last_login_at')
                    ->form([
                        Forms\Components\DatePicker::make('last_login_from')
                            ->label('Last login from'),
                        Forms\Components\DatePicker::make('last_login_until')
                            ->label('Last login until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['last_login_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('last_login_at', '>=', $date),
                            )
                            ->when(
                                $data['last_login_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('last_login_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SavedPropertiesRelationManager::class,
            RelationManagers\SavedSearchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
