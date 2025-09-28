<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\MaintenanceRequestResource\Pages;
use App\Filament\Tenant\Resources\MaintenanceRequestResource\RelationManagers;
use App\Models\MaintenanceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Maintenance Requests';

    protected static ?string $modelLabel = 'Maintenance Request';

    protected static ?string $pluralModelLabel = 'Maintenance Requests';

    protected static ?string $navigationGroup = 'Requests & Support';

    protected static ?int $navigationSort = 3;

    // Hide maintenance requests feature for now
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Details')
                    ->description('Describe the maintenance issue or request')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Forms\Components\Select::make('property_id')
                                    ->label('Property')
                                    ->relationship('property', 'title', function (Builder $query) {
                                        $user = Auth::user();
                                        $tenant = $user->tenant;

                                        if ($tenant) {
                                            // Get properties from active leases for this tenant
                                            $propertyIds = \App\Models\Lease::where('tenant_id', $tenant->id)
                                                ->where('status', 'active')
                                                ->pluck('property_id');

                                            return $query->whereIn('id', $propertyIds);
                                        }

                                        return $query->whereRaw('1 = 0'); // No properties if no tenant
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2)
                                    ->default(function () {
                                        $user = Auth::user();
                                        $tenant = $user->tenant;

                                        if ($tenant) {
                                            // Auto-select if tenant has only one active lease
                                            $activeLease = \App\Models\Lease::where('tenant_id', $tenant->id)
                                                ->where('status', 'active')
                                                ->first();

                                            return $activeLease?->property_id;
                                        }

                                        return null;
                                    }),
                                    
                                Forms\Components\Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'plumbing' => 'Plumbing',
                                        'electrical' => 'Electrical',
                                        'hvac' => 'HVAC/Air Conditioning',
                                        'appliances' => 'Appliances',
                                        'structural' => 'Structural',
                                        'security' => 'Security',
                                        'cleaning' => 'Cleaning',
                                        'pest_control' => 'Pest Control',
                                        'landscaping' => 'Landscaping',
                                        'other' => 'Other',
                                    ])
                                    ->required()
                                    ->columnSpan(1),
                                    
                                Forms\Components\Select::make('priority')
                                    ->label('Priority Level')
                                    ->options([
                                        'low' => 'Low - Non-urgent',
                                        'medium' => 'Medium - Normal',
                                        'high' => 'High - Urgent',
                                        'emergency' => 'Emergency - Immediate attention needed',
                                    ])
                                    ->required()
                                    ->default('medium')
                                    ->columnSpan(1),
                                    
                                Forms\Components\TextInput::make('title')
                                    ->label('Request Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                            ]),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Detailed Description')
                            ->required()
                            ->rows(4)
                            ->hint('Please provide as much detail as possible about the issue')
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('location')
                            ->label('Specific Location')
                            ->hint('e.g., "Master bedroom bathroom", "Kitchen sink", "Living room"')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->collapsible(),

                Forms\Components\Section::make('Availability & Access')
                    ->description('When can maintenance be performed?')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Forms\Components\DateTimePicker::make('preferred_date')
                                    ->label('Preferred Date & Time')
                                    ->hint('When would you prefer this to be addressed?')
                                    ->columnSpan(1),
                                    
                                Forms\Components\Select::make('access_instructions')
                                    ->label('Property Access')
                                    ->options([
                                        'tenant_present' => 'I will be present',
                                        'key_available' => 'Key available with landlord/agent',
                                        'spare_key' => 'Spare key hidden (will provide location)',
                                        'arrange_access' => 'Need to arrange access',
                                    ])
                                    ->columnSpan(1),
                            ]),
                            
                        Forms\Components\Textarea::make('access_notes')
                            ->label('Access Notes')
                            ->hint('Any special instructions for accessing the property')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->collapsible(),

                Forms\Components\Section::make('Contact Information')
                    ->description('How should we contact you?')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Contact Phone')
                                    ->tel()
                                    ->columnSpan(1),
                                    
                                Forms\Components\Select::make('preferred_contact_method')
                                    ->label('Preferred Contact Method')
                                    ->options([
                                        'phone' => 'Phone Call',
                                        'sms' => 'SMS/Text',
                                        'email' => 'Email',
                                        'app' => 'In-App Notification',
                                    ])
                                    ->default('phone')
                                    ->columnSpan(1),
                            ]),
                    ])->collapsible(),

                Forms\Components\Section::make('Status & Updates')
                    ->description('Request status and progress updates')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('status')
                                    ->label('Current Status')
                                    ->disabled()
                                    ->default('pending')
                                    ->columnSpan(1),
                                    
                                Forms\Components\DateTimePicker::make('scheduled_date')
                                    ->label('Scheduled Date')
                                    ->disabled()
                                    ->columnSpan(1),
                            ]),
                            
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Administrator Notes')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('completion_notes')
                            ->label('Completion Notes')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->collapsible()->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Request Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->colors([
                        'info' => 'plumbing',
                        'warning' => 'electrical',
                        'success' => 'hvac',
                        'primary' => 'appliances',
                        'danger' => 'structural',
                        'gray' => 'security',
                        'secondary' => ['cleaning', 'pest_control', 'landscaping', 'other'],
                    ])
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->colors([
                        'success' => 'low',
                        'primary' => 'medium',
                        'warning' => 'high',
                        'danger' => 'emergency',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucwords($state)),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'in_progress',
                        'info' => 'scheduled',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('preferred_date')
                    ->label('Preferred Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Scheduled')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'plumbing' => 'Plumbing',
                        'electrical' => 'Electrical',
                        'hvac' => 'HVAC/Air Conditioning',
                        'appliances' => 'Appliances',
                        'structural' => 'Structural',
                        'security' => 'Security',
                        'cleaning' => 'Cleaning',
                        'pest_control' => 'Pest Control',
                        'landscaping' => 'Landscaping',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'emergency' => 'Emergency',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (MaintenanceRequest $record): bool => 
                        in_array($record->status, ['pending', 'scheduled'])
                    ),
                Tables\Actions\Action::make('cancel_request')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (MaintenanceRequest $record): bool => 
                        in_array($record->status, ['pending', 'scheduled'])
                    )
                    ->requiresConfirmation()
                    ->action(function (MaintenanceRequest $record) {
                        $record->update(['status' => 'cancelled']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => false), // Disable bulk delete for safety
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        // Get the current user and find their tenant record
        $user = Auth::user();
        $tenant = $user->tenant ?? null;
        
        if (!$tenant) {
            // If no tenant record exists, return empty query
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('tenant_id', $tenant->id)
            ->with(['property', 'tenant']);
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
            'index' => Pages\ListMaintenanceRequests::route('/'),
            'create' => Pages\CreateMaintenanceRequest::route('/create'),
            'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        // Tenants can only edit pending or scheduled requests
        return in_array($record->status, ['pending', 'scheduled']);
    }

    public static function canDelete(Model $record): bool
    {
        // Tenants can only delete pending requests
        return $record->status === 'pending';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set tenant_id when creating new request
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if ($tenant) {
            $data['tenant_id'] = $tenant->id;
        }
        
        // Set default status
        $data['status'] = 'pending';
        
        return $data;
    }
}
