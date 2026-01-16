<?php

namespace App\Filament\Agent\Pages;

use Filament\Pages\Page;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;

class MyProperties extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected string $view = 'filament.agent.pages.my-properties';
    
    // Hide from sidebar navigation (we only want it on mobile bottom nav)
    protected static bool $shouldRegisterNavigation = false;

    public $properties;

    public function mount()
    {
        $this->loadProperties();
    }

    public function loadProperties()
    {
        $user = Auth::user();
        $agent = $user?->agentProfile;
        
        if ($agent) {
            $this->properties = Property::where('agent_id', $agent->id)
                ->orderBy('created_at', 'desc')
                ->with(['media', 'propertyType'])
                ->get();
        } else {
            $this->properties = collect();
        }
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalHeading('Delete Property')
            ->modalDescription('Are you sure you want to delete this property? This cannot be undone.')
            ->modalSubmitActionLabel('Yes, delete it')
            ->color('danger')
            ->action(function (array $arguments) {
                $user = Auth::user();
                $agentId = $user?->agentProfile?->id;

                if (!$agentId) return;

                $property = Property::where('id', $arguments['record'])
                    ->where('agent_id', $agentId)
                    ->first();

                if ($property) {
                    $property->delete();
                    Notification::make()
                        ->success()
                        ->title('Property Deleted')
                        ->send();
                    
                    $this->loadProperties();
                }
            });
    }

    public function changeStatusAction(): Action
    {
        return Action::make('changeStatus')
            ->label('Change Status')
            ->modalHeading('Update Property Status')
            ->modalWidth('sm')
            ->fillForm(fn (array $arguments) => [
                'status' => Property::find($arguments['record'])?->status
            ])
            ->form([
                Select::make('status')
                    ->label('New Status')
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                    ])
                    ->required()
                    ->native(false)
            ])
            ->action(function (array $arguments, array $data) {
                $user = Auth::user();
                $agentId = $user?->agentProfile?->id;
                
                if (!$agentId) return;

                $property = Property::where('id', $arguments['record'])
                    ->where('agent_id', $agentId)
                    ->first();

                if ($property) {
                    $property->update(['status' => $data['status']]);
                    Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->send();
                    
                    $this->loadProperties();
                }
            });
    }
}
