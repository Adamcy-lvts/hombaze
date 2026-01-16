<?php

namespace App\Filament\Agent\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Agent\Resources\LeaseTemplateResource\Pages;
use App\Models\LeaseTemplate;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Colors\Color;

class LeaseTemplateResource extends Resource
{
    protected static ?string $model = LeaseTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string | \UnitEnum | null $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Lease Templates';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template Information')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Template Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Standard Residential Lease'),

                                Toggle::make('is_default')
                                    ->label('Set as Default Template')
                                    ->helperText('This template will be pre-selected when creating new leases'),
                            ]),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Brief description of when to use this template'),
                    ]),

                Section::make('Default Lease Settings')
                    ->description('These values will be pre-filled when creating leases from this template')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('default_payment_frequency')
                                    ->label('Payment Frequency')
                                    ->options(LeaseTemplate::getPaymentFrequencies())
                                    ->required()
                                    ->default('annually'),

                                TextInput::make('default_grace_period_days')
                                    ->label('Grace Period (Days)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                Toggle::make('default_renewal_option')
                                    ->label('Renewal Available')
                                    ->default(true),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('default_security_deposit')
                                    ->label('Security Deposit')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->placeholder('Leave empty if varies by property'),

                                TextInput::make('default_service_charge')
                                    ->label('Service Charge')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->placeholder('Leave empty if not applicable'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('default_legal_fee')
                                    ->label('Legal Fee')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->placeholder('Leave empty if not applicable'),

                                TextInput::make('default_agency_fee')
                                    ->label('Agency Fee')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->placeholder('Leave empty if not applicable'),

                                TextInput::make('default_caution_deposit')
                                    ->label('Caution Deposit')
                                    ->numeric()
                                    ->prefix('₦')
                                    ->placeholder('Leave empty if not applicable'),
                            ]),
                    ]),


                Section::make('Terms & Conditions Template')
                    ->description('Write your lease terms using merge tags. The merge tags panel will open automatically to help you insert dynamic variables.')
                    ->columnSpanFull()
                    ->schema([
                        RichEditor::make('terms_and_conditions')
                            ->label('Terms & Conditions')
                            ->required()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->mergeTags(array_flip(LeaseTemplate::getAvailableVariables()))
                            ->activePanel('mergeTags')
                            ->default(LeaseTemplate::getDefaultContent())

                    ]),

                Section::make('Template Status')
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Template Active')
                            ->default(true)
                            ->helperText('Inactive templates cannot be used to create new leases'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Agent uses 'landlord_id' column for their templates? 
                // Assumed yes, as they act as a landlord entity in some contexts or have their own ID there.
                // Keeping consistent with previous files using Auth::id() for templates.
                return $query->where('landlord_id', Auth::id());
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Template Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('default_payment_frequency')
                    ->label('Payment Frequency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'monthly' => 'info',
                        'quarterly' => 'warning',
                        'biannually' => 'success',
                        'annually' => 'primary',
                        default => 'gray',
                    }),

                IconColumn::make('default_renewal_option')
                    ->label('Renewable')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('leases_count')
                    ->label('Used in Leases')
                    ->counts('leases')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Templates')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),

                TernaryFilter::make('is_default')
                    ->label('Default Template')
                    ->boolean()
                    ->trueLabel('Default only')
                    ->falseLabel('Non-default only')
                    ->native(false),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('setDefault')
                    ->label('Set Default')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function (LeaseTemplate $record) {
                        $record->setAsDefault();
                        
                        Notification::make()
                            ->title('Default template updated')
                            ->body($record->name . ' is now your default template.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (LeaseTemplate $record) => !$record->is_default && $record->is_active),
                    
                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(function (LeaseTemplate $record) {
                        $newTemplate = $record->replicate();
                        $newTemplate->name = $record->name . ' (Copy)';
                        $newTemplate->is_default = false;
                        $newTemplate->save();
                        
                        Notification::make()
                            ->title('Template duplicated')
                            ->body('New template created: ' . $newTemplate->name)
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListLeaseTemplates::route('/'),
            'create' => Pages\CreateLeaseTemplate::route('/create'),
            'view' => Pages\ViewLeaseTemplate::route('/{record}'),
            'edit' => Pages\EditLeaseTemplate::route('/{record}/edit'),
        ];
    }
}
