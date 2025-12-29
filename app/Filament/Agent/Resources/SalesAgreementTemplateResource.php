<?php

namespace App\Filament\Agent\Resources;

use App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages\CreateSalesAgreementTemplate;
use App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages\EditSalesAgreementTemplate;
use App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages\ListSalesAgreementTemplates;
use App\Filament\Agent\Resources\SalesAgreementTemplateResource\Pages\ViewSalesAgreementTemplate;
use App\Models\SalesAgreementTemplate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalesAgreementTemplateResource extends Resource
{
    protected static ?string $model = SalesAgreementTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string | \UnitEnum | null $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Sales Agreement Templates';

    protected static ?int $navigationSort = 4;

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
                                    ->placeholder('e.g., Standard Sales Agreement'),

                                Toggle::make('is_default')
                                    ->label('Set as Default Template')
                                    ->helperText('This template will be pre-selected when generating agreements'),
                            ]),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Brief description of when to use this template'),
                    ]),

                Section::make('Terms & Conditions Template')
                    ->description('Write your sales agreement terms using merge tags for dynamic data.')
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
                            ->mergeTags(array_flip(SalesAgreementTemplate::getAvailableVariables()))
                            ->activePanel('mergeTags'),
                    ]),

                Section::make('Template Status')
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Template Active')
                            ->default(true)
                            ->helperText('Inactive templates cannot be used to generate agreements'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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

                TextColumn::make('sales_agreements_count')
                    ->label('Used in Agreements')
                    ->counts('salesAgreements')
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
                    ->action(function (SalesAgreementTemplate $record) {
                        $record->setAsDefault();

                        Notification::make()
                            ->title('Default template updated')
                            ->body($record->name . ' is now your default template.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (SalesAgreementTemplate $record) => !$record->is_default && $record->is_active),

                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(function (SalesAgreementTemplate $record) {
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
            'index' => ListSalesAgreementTemplates::route('/'),
            'create' => CreateSalesAgreementTemplate::route('/create'),
            'view' => ViewSalesAgreementTemplate::route('/{record}'),
            'edit' => EditSalesAgreementTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $agentId = Auth::user()?->agentProfile?->id;
        $query = parent::getEloquentQuery();

        if (! $agentId) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('agent_id', $agentId)
            ->withCount('salesAgreements');
    }
}
