<?php

namespace App\Filament\Landlord\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Landlord\Resources\MaintenanceRequestResource\Pages\ListMaintenanceRequests;
use App\Filament\Landlord\Resources\MaintenanceRequestResource\Pages\CreateMaintenanceRequest;
use App\Filament\Landlord\Resources\MaintenanceRequestResource\Pages\EditMaintenanceRequest;
use App\Filament\Landlord\Resources\MaintenanceRequestResource\Pages;
use App\Filament\Landlord\Resources\MaintenanceRequestResource\RelationManagers;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Lease;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Maintenance Requests';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Request Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('property_id')
                                    ->label('Property')
                                    ->relationship('property', 'title', function (Builder $query) {
                                        return $query->whereHas('owner', function (Builder $query) {
                                            $query->where('user_id', Auth::id());
                                        });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'first_name', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('lease_id')
                                    ->label('Lease')
                                    ->relationship('lease', 'id', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                                        $record->property->title . ' - ' . $record->tenant->name
                                    )
                                    ->searchable()
                                    ->preload(),

                                Select::make('priority')
                                    ->options(MaintenanceRequest::getPriorities())
                                    ->required()
                                    ->default('medium'),
                            ]),
                    ]),

                Section::make('Request Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000),

                        Select::make('category')
                            ->options(MaintenanceRequest::getCategories())
                            ->required(),

                        Select::make('urgency')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                                'emergency' => 'Emergency',
                            ])
                            ->required()
                            ->default('normal'),
                    ]),

                Section::make('Status & Scheduling')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->options(MaintenanceRequest::getStatuses())
                                    ->required()
                                    ->default('submitted'),

                                DateTimePicker::make('requested_date')
                                    ->default(now())
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('scheduled_date'),

                                DateTimePicker::make('completed_date'),
                            ]),
                    ]),

                Section::make('Cost & Contact')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('estimated_cost')
                                    ->numeric()
                                    ->prefix('₦'),

                                TextInput::make('actual_cost')
                                    ->numeric()
                                    ->prefix('₦'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('contact_name')
                                    ->maxLength(255),

                                TextInput::make('contact_phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('landlord_notes')
                            ->label('Landlord Notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Textarea::make('contractor_notes')
                            ->label('Contractor Notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Toggle::make('requires_tenant_access')
                            ->label('Requires Tenant Access')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->badge(),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'gray',
                        'acknowledged' => 'info',
                        'in_progress' => 'warning',
                        'scheduled' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'on_hold' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('urgency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'emergency' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('requested_date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('estimated_cost')
                    ->money('NGN')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(MaintenanceRequest::getStatuses()),

                SelectFilter::make('priority')
                    ->options(MaintenanceRequest::getPriorities()),

                SelectFilter::make('category')
                    ->options(MaintenanceRequest::getCategories()),

                SelectFilter::make('urgency')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'emergency' => 'Emergency',
                    ]),

                Filter::make('open_requests')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status', ['submitted', 'acknowledged', 'in_progress', 'scheduled']))
                    ->label('Open Requests'),

                Filter::make('emergency')
                    ->query(fn (Builder $query): Builder => $query->where('urgency', 'emergency'))
                    ->label('Emergency Requests'),
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
            'index' => ListMaintenanceRequests::route('/'),
            'create' => CreateMaintenanceRequest::route('/create'),
            'edit' => EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }
}
