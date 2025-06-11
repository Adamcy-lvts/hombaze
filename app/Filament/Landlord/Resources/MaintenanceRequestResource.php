<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\MaintenanceRequestResource\Pages;
use App\Filament\Landlord\Resources\MaintenanceRequestResource\RelationManagers;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Lease;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Maintenance Requests';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('property_id')
                                    ->label('Property')
                                    ->relationship('property', 'title', function (Builder $query) {
                                        return $query->whereHas('owner', function (Builder $query) {
                                            $query->where('user_id', Auth::id());
                                        });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'first_name', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('lease_id')
                                    ->label('Lease')
                                    ->relationship('lease', 'id', function (Builder $query) {
                                        return $query->where('landlord_id', Auth::id());
                                    })
                                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                                        $record->property->title . ' - ' . $record->tenant->name
                                    )
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('priority')
                                    ->options(MaintenanceRequest::getPriorities())
                                    ->required()
                                    ->default('medium'),
                            ]),
                    ]),

                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000),

                        Forms\Components\Select::make('category')
                            ->options(MaintenanceRequest::getCategories())
                            ->required(),

                        Forms\Components\Select::make('urgency')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                                'emergency' => 'Emergency',
                            ])
                            ->required()
                            ->default('normal'),
                    ]),

                Forms\Components\Section::make('Status & Scheduling')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options(MaintenanceRequest::getStatuses())
                                    ->required()
                                    ->default('submitted'),

                                Forms\Components\DateTimePicker::make('requested_date')
                                    ->default(now())
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('scheduled_date'),

                                Forms\Components\DateTimePicker::make('completed_date'),
                            ]),
                    ]),

                Forms\Components\Section::make('Cost & Contact')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('estimated_cost')
                                    ->numeric()
                                    ->prefix('₦'),

                                Forms\Components\TextInput::make('actual_cost')
                                    ->numeric()
                                    ->prefix('₦'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('contact_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('contact_phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('landlord_notes')
                            ->label('Landlord Notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\Textarea::make('contractor_notes')
                            ->label('Contractor Notes')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\Toggle::make('requires_tenant_access')
                            ->label('Requires Tenant Access')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge(),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
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

                Tables\Columns\TextColumn::make('urgency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'emergency' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('requested_date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimated_cost')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(MaintenanceRequest::getStatuses()),

                Tables\Filters\SelectFilter::make('priority')
                    ->options(MaintenanceRequest::getPriorities()),

                Tables\Filters\SelectFilter::make('category')
                    ->options(MaintenanceRequest::getCategories()),

                Tables\Filters\SelectFilter::make('urgency')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'emergency' => 'Emergency',
                    ]),

                Tables\Filters\Filter::make('open_requests')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status', ['submitted', 'acknowledged', 'in_progress', 'scheduled']))
                    ->label('Open Requests'),

                Tables\Filters\Filter::make('emergency')
                    ->query(fn (Builder $query): Builder => $query->where('urgency', 'emergency'))
                    ->label('Emergency Requests'),
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
            'index' => Pages\ListMaintenanceRequests::route('/'),
            'create' => Pages\CreateMaintenanceRequest::route('/create'),
            'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }
}
