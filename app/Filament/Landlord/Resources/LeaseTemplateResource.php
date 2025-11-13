<?php

namespace App\Filament\Landlord\Resources;

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
use App\Filament\Landlord\Resources\LeaseTemplateResource\Pages\ListLeaseTemplates;
use App\Filament\Landlord\Resources\LeaseTemplateResource\Pages\CreateLeaseTemplate;
use App\Filament\Landlord\Resources\LeaseTemplateResource\Pages\ViewLeaseTemplate;
use App\Filament\Landlord\Resources\LeaseTemplateResource\Pages\EditLeaseTemplate;
use App\Filament\Landlord\Resources\LeaseTemplateResource\Pages;
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

                Section::make('Template Variables')
                    ->description('Available variables you can use in your terms and conditions')
                    ->schema([
                        Placeholder::make('available_variables')
                            ->label('Available Variables')
                            ->content(function () {
                                $variables = LeaseTemplate::getAvailableVariables();
                                $variableList = '';
                                
                                foreach ($variables as $key => $label) {
                                    $variableList .= "• **{{" . $key . "}}** - " . $label . "\n";
                                }
                                
                                return new HtmlString(
                                    '<div class="text-sm space-y-1">' .
                                    '<p class="font-medium text-gray-700">Use these variables in your terms and conditions:</p>' .
                                    '<div class="bg-gray-50 p-3 rounded-sm border max-h-40 overflow-y-auto">' .
                                    '<pre class="whitespace-pre-wrap text-xs">' . $variableList . '</pre>' .
                                    '</div>' .
                                    '<p class="text-xs text-gray-500 mt-2">Variables will be automatically replaced with actual values when creating leases.</p>' .
                                    '</div>'
                                );
                            }),
                    ])
                    ->collapsible(),

                Section::make('Terms & Conditions Template')
                    ->description('Write your lease terms using the variables above. They will be replaced with actual values when creating leases.')
                    ->schema([
                        RichEditor::make('terms_and_conditions')
                            ->label('Terms & Conditions')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ])
                            ->default('
<h3>Standard Lease Terms</h3>
<ol>
<li>The tenant <strong>{{tenant_name}}</strong> agrees to pay rent <strong>{{payment_frequency}}</strong> in the amount of <strong>{{rent_amount}}</strong> for the property located at <strong>{{property_address}}</strong>.</li>
<li>The lease term shall commence on <strong>{{lease_start_date}}</strong> and terminate on <strong>{{lease_end_date}}</strong>, for a total duration of <strong>{{lease_duration_months}}</strong> months.</li>
<li>The tenant shall use the premises <strong>solely for residential purposes</strong> and shall not conduct any business activities without prior written consent from the landlord <strong>{{landlord_name}}</strong>.</li>
<li>The tenant shall <strong>maintain the premises in good condition</strong> and shall be responsible for any damages beyond normal wear and tear.</li>
<li>The tenant shall <strong>not sublease, assign, or transfer</strong> any rights under this agreement without written consent from the landlord.</li>
<li>The tenant shall <strong>comply with all applicable laws, regulations, and community rules</strong> and shall not engage in any illegal activities on the premises.</li>
<li>The landlord shall <strong>maintain the structural integrity</strong> of the property and ensure all major systems (plumbing, electrical, etc.) are in working order.</li>
<li>Either party may <strong>terminate this agreement with 30 days written notice</strong>, subject to applicable local laws and regulations.</li>
<li>Renewal Option: <strong>{{renewal_option}}</strong> - This lease may be renewed upon mutual agreement of both parties before the expiration date.</li>
<li>This agreement is executed on <strong>{{current_date}}</strong> in <strong>{{current_year}}</strong>.</li>
</ol>
                            ')
                            ->helperText('Use the variables from the section above (e.g., {{property_title}}, {{tenant_name}}, {{rent_amount}}) to create dynamic templates.'),
                    ]),

                Section::make('Template Status')
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
            ->recordActions([
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
            ->toolbarActions([
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
            'index' => ListLeaseTemplates::route('/'),
            'create' => CreateLeaseTemplate::route('/create'),
            'view' => ViewLeaseTemplate::route('/{record}'),
            'edit' => EditLeaseTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('landlord_id', Auth::id())
            ->withCount('leases');
    }
}