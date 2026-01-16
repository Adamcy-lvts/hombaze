<?php

namespace App\Filament\Agency\Pages;

use App\Models\PropertyInquiry;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class Inquiries extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected string $view = 'filament.agency.pages.inquiries';

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
                        $agency = Filament::getTenant();
                        
                        if ($agency) {
                            $query->where('agency_id', $agency->id);
                        } else {
                            // No agency context - return nothing
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
                fn (PropertyInquiry $record): string => route('filament.agency.resources.property-inquiries.edit', [
                    'tenant' => Filament::getTenant()?->slug,
                    'record' => $record
                ]),
            )
            ->defaultSort('created_at', 'desc');
    }
}
