<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\ListingPackage;
use App\Models\SmartSearchSubscription;
use App\Services\ListingCreditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Filament\Resources\UserResource\RelationManagers\SavedPropertiesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\SmartSearchesRelationManager;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Profile Avatar Section (Top)
                Section::make('Profile Picture')
                    ->description('Upload and manage user profile picture')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->image()
                            ->disk('public')
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
                Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'lg' => 3,
                ])
                    ->schema([
                        // Left Column - User Information & Security
                        Group::make()
                            ->schema([
                                Section::make('User Information')
                                    ->description('Basic user account information and credentials')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                TextInput::make('phone')
                                                    ->tel()
                                                    ->maxLength(255)
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                Select::make('user_type')
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

                                Section::make('Security')
                                    ->description('Account security and authentication settings')
                                    ->schema([
                                        TextInput::make('password')
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
                        Group::make()
                            ->schema([
                                Section::make('Account Status')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 1,
                                        ])
                                            ->schema([
                                                Toggle::make('is_active')
                                                    ->label('Account Active')
                                                    ->helperText('Active accounts can log in')
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                        'lg' => 1,
                                                    ]),
                                                Toggle::make('is_verified')
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

                                Section::make('Verification Dates')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 1,
                                        ])
                                            ->schema([
                                                DateTimePicker::make('email_verified_at')
                                                    ->label('Email Verified At')
                                                    ->disabled()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                DateTimePicker::make('phone_verified_at')
                                                    ->label('Phone Verified At')
                                                    ->disabled()
                                                    ->columnSpan([
                                                        'default' => 1,
                                                        'sm' => 1,
                                                    ]),
                                                DateTimePicker::make('last_login_at')
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->copyable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('user_type')
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
                TextColumn::make('agencies.name')
                    ->label('Agency'),
                IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Email Verified')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Login')
                    ->toggleable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Email Verified At')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Phone Verified At')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_type')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'agency_owner' => 'Agency Owner',
                        'agent' => 'Agent',
                        'property_owner' => 'Property Owner',
                        'tenant' => 'Tenant',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All users')
                    ->trueLabel('Active users')
                    ->falseLabel('Inactive users'),
                TernaryFilter::make('is_verified')
                    ->label('Email Verification')
                    ->placeholder('All users')
                    ->trueLabel('Verified users')
                    ->falseLabel('Unverified users'),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Created from'),
                        DatePicker::make('created_until')
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
                Filter::make('last_login_at')
                    ->schema([
                        DatePicker::make('last_login_from')
                            ->label('Last login from'),
                        DatePicker::make('last_login_until')
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ActionGroup::make([
                    Action::make('grantSmartSearchPlan')
                        ->label('Grant SmartSearch Plan')
                        ->icon('heroicon-o-magnifying-glass')
                        ->form([
                            Select::make('plan_id')
                                ->label('Plan')
                                ->options(DB::table('smart_search_plans')
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $plan = DB::table('smart_search_plans')->where('id', $data['plan_id'] ?? null)->first();
                            if (!$plan) {
                                return;
                            }
                            $channels = $plan->notification_channels ?? '[]';
                            if (is_string($channels)) {
                                $decoded = json_decode($channels, true);
                                $channels = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                            }

                            SmartSearchSubscription::create([
                                'user_id' => $record->id,
                                'tier' => $plan->slug,
                                'searches_limit' => (int) $plan->searches_limit,
                                'searches_used' => 0,
                                'duration_days' => (int) $plan->duration_days,
                                'amount_paid' => 0,
                                'payment_reference' => Str::uuid()->toString(),
                                'payment_method' => 'admin_grant',
                                'payment_status' => 'paid',
                                'paid_at' => now(),
                                'starts_at' => now(),
                                'expires_at' => now()->addDays((int) $plan->duration_days),
                                'notification_channels' => $channels,
                                'notes' => 'Admin granted plan',
                            ]);
                        }),
                    Action::make('grantListingPackage')
                        ->label('Grant Listing Package')
                        ->icon('heroicon-o-briefcase')
                        ->form([
                            Select::make('package_id')
                                ->label('Package')
                                ->options(ListingPackage::query()
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $package = ListingPackage::find($data['package_id']);
                            if ($package) {
                                ListingCreditService::grantPackage($record, $package, 'admin_grant');
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SavedPropertiesRelationManager::class,
            SmartSearchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
