<?php

namespace App\Livewire\Customer;

use App\Models\PropertyInquiry;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Inquiries extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public $statusFilter = '';

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'sort')]
    public $sortBy = 'newest';

    public function mount()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function getStats()
    {
        $userId = auth()->id();

        return [
            'total' => PropertyInquiry::where('inquirer_id', $userId)->count(),
            'pending' => PropertyInquiry::where('inquirer_id', $userId)->where('status', 'new')->count(),
            'responded' => PropertyInquiry::where('inquirer_id', $userId)->where('status', 'contacted')->count(),
            'viewed' => PropertyInquiry::where('inquirer_id', $userId)->where('status', 'viewed')->count(),
        ];
    }

    public function getInquiries()
    {
        $query = PropertyInquiry::where('inquirer_id', auth()->id())
            ->with(['property.propertyType', 'property.area', 'property.city']);

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('message', 'like', '%' . $this->search . '%')
                  ->orWhere('inquirer_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('property', function($propertyQuery) {
                      $propertyQuery->where('title', 'like', '%' . $this->search . '%')
                                   ->orWhere('address', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'property_title':
                $query->join('properties', 'property_inquiries.property_id', '=', 'properties.id')
                      ->orderBy('properties.title', 'asc')
                      ->select('property_inquiries.*');
                break;
            case 'status':
                $query->orderBy('status', 'asc')->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(6);
    }

    public function markAsRead($inquiryId)
    {
        $inquiry = PropertyInquiry::where('id', $inquiryId)
            ->where('inquirer_id', auth()->id())
            ->first();

        if ($inquiry && $inquiry->status === 'new') {
            $inquiry->update(['status' => 'viewed']);
            $this->dispatch('inquiry-updated', 'Inquiry marked as read');
        }
    }

    public function deleteInquiry($inquiryId)
    {
        $inquiry = PropertyInquiry::where('id', $inquiryId)
            ->where('inquirer_id', auth()->id())
            ->first();

        if ($inquiry) {
            $inquiry->delete();
            $this->dispatch('inquiry-deleted', 'Inquiry deleted successfully');
        }
    }

    public function render()
    {
        return view('livewire.customer.inquiries', [
            'inquiries' => $this->getInquiries(),
            'stats' => $this->getStats(),
        ])->layout('layouts.guest-app');
    }
}