<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyFeed extends Component
{
    use WithPagination;

    public $perPage = 5;

    public array $savedPropertyIds = [];

    protected \App\Services\PropertyWishlistService $wishlistService;

    public function boot(\App\Services\PropertyWishlistService $wishlistService): void
    {
        $this->wishlistService = $wishlistService;
    }

    public function mount(): void
    {
        $this->loadSavedProperties();
    }

    private function loadSavedProperties(): void
    {
        if (auth()->check()) {
            $this->savedPropertyIds = $this->wishlistService->getSavedPropertyIds(auth()->user());
        }
    }

    public function toggleSaveProperty(int $propertyId): void
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please login to save properties.');
            $this->redirect(route('login'));
            return;
        }

        $isSaved = $this->wishlistService->toggleSave(auth()->user(), $propertyId);

        if ($isSaved) {
            $this->savedPropertyIds[] = $propertyId;
            $this->dispatch('property-saved', message: 'Property saved successfully');
        } else {
            $this->savedPropertyIds = array_values(array_diff($this->savedPropertyIds, [$propertyId]));
            $this->dispatch('property-unsaved', message: 'Property removed from saved list');
        }
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }

    public function render()
    {
        $properties = Property::query()
            ->with(['city', 'state', 'media', 'owner.user'])
            ->where('status', 'available')
            ->where('moderation_status', 'approved')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.property-feed', [
            'properties' => $properties
        ])->layout('layouts.guest-app');
    }
}
