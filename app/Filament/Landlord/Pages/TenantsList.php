<?php

namespace App\Filament\Landlord\Pages;

use Filament\Pages\Page;
use App\Models\Tenant;
use Livewire\WithPagination;

class TenantsList extends Page
{
    use WithPagination;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected string $view = 'filament.landlord.pages.tenants-list';

    protected static bool $shouldRegisterNavigation = false;


    public ?Tenant $selectedTenant = null;
    public $search = '';

    protected static ?string $title = 'Tenants';

    public function viewTenant($id)
    {
        $this->selectedTenant = Tenant::with('leases')->find($id);
    }

    public function closeTenantView()
    {
        $this->selectedTenant = null;
    }


    public function getTenantsProperty()
    {
        return Tenant::query()
            ->where('landlord_id', \Illuminate\Support\Facades\Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('first_name', 'like', '%' . $this->search . '%')
                          ->orWhere('last_name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%')
                          ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
