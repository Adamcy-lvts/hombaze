<?php

namespace App\Filament\Agent\Pages;

use App\Models\Lease;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class MobileLeases extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.agent.pages.mobile-leases';

    protected static ?string $slug = 'mobile-leases';

    protected static ?string $title = 'Leases';
    
    // Hide from standard sidebar navigation (linked manually)
    protected static bool $shouldRegisterNavigation = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lease::query()
                    ->whereHas('property', function (Builder $query) {
                         $query->whereHas('agent', function (Builder $q) {
                            $q->where('user_id', Auth::id());
                        });
                    })
            )
            ->columns([
               // We will use a custom view loop in the page blade, so columns here are strictly for data retrieval if needed by the table object,
               // but we will likely iterate over getRecords() manually.
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Lease::getStatuses()),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
