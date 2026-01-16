<?php

namespace App\Filament\Agent\Pages;

use App\Models\PropertyInquiry;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;

class Inquiries extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected string $view = 'filament.agent.pages.inquiries';

    protected static ?string $slug = 'inquiries';

    protected static ?string $title = 'Inquiries';
    
    // Hide from standard sidebar navigation (since we are linking it manually)
    protected static bool $shouldRegisterNavigation = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PropertyInquiry::query()
                    ->whereHas('property', function (Builder $query) {
                        $user = auth()->user();
                        
                        // Check if user is an agent
                         $agent = \App\Models\Agent::where('user_id', $user->id)->first();
                         
                         if ($agent) {
                             $query->where('agent_id', $agent->id)
                                   ->whereNull('agency_id');
                         } else {
                             // Fallback or potentially handle agency logic if user belongs to one directly? 
                             // But for now, if not an agent, empty result to be safe.
                             $query->whereRaw('1 = 0');
                         }
                    })
            )
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
                'default' => 1,
            ])
            ->columns([
                // Columns are not used for rendering, but we keep the method for specific table configurations if needed.
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'responded' => 'Responded',
                        'viewing_scheduled' => 'Viewing Scheduled',
                        'closed' => 'Closed',
                    ]),
            ])
            ->recordUrl(
                fn (PropertyInquiry $record): string => route('filament.agent.resources.property-inquiries.edit', ['record' => $record]),
            )
            ->defaultSort('created_at', 'desc');
    }
}
