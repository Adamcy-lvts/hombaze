<?php

namespace App\Livewire\Customer;

use App\Models\SmartSearch;
use App\Jobs\ProcessSmartSearchMatches;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class SearchManager extends Component
{
    use WithPagination;

    public $recentlyCreatedSearchId = null;
    public $searchJobStatus = null;

    public function mount()
    {
        // Check for recently created search
        $this->recentlyCreatedSearchId = session('created_search_id');
        if ($this->recentlyCreatedSearchId) {
            $this->searchJobStatus = 'searching';
            session()->forget('created_search_id');

            // Auto-timeout after 30 seconds if no WebSocket response
            $this->dispatch('start-search-timeout', searchId: $this->recentlyCreatedSearchId);
        }
    }

    public function render()
    {
        $searches = Auth::user()->smartSearches()
            ->latest()
            ->paginate(10);

        // Get recent match notifications for each search
        // Get recent match notifications for each search
        $searchesWithMatches = $searches->getCollection()->map(function ($search) {
            // Get recent SmartSearchMatch notifications for this search
            $recentNotifications = Auth::user()->notifications()
                ->where('type', 'App\\Notifications\\SmartSearchMatch')
                ->where('data->smart_search_id', $search->id)
                ->where('created_at', '>=', now()->subDays(7)) // Last 7 days
                ->get(); // Get more notifications to ensure we capture multiple unique properties

            // Extract unique property IDs from notifications
            $propertyIds = collect();
            foreach ($recentNotifications as $notification) {
                if (isset($notification->data['properties']) && is_array($notification->data['properties'])) {
                    foreach ($notification->data['properties'] as $propData) {
                        if (isset($propData['id'])) {
                            $propertyIds->push($propData['id']);
                        }
                    }
                }
            }
            
            $uniquePropertyIds = $propertyIds->unique()->values();
            
            // Fetch actual property models with media to ensure we have thumbnails
            // We take top 5 most recent matches (assuming implicit order if we sort by id desc or similar, but here just taking any valid ones)
            $properties = \App\Models\Property::whereIn('id', $uniquePropertyIds)
                ->with(['media', 'city', 'area'])
                ->latest()
                ->get(); // We can limit in view if needed, but let's get them all for accurate count

            $search->matched_properties = $properties;
            $search->has_matches = $properties->isNotEmpty();
            $search->recent_matches_count = $properties->count();

            return $search;
        });

        $searches->setCollection($searchesWithMatches);

        return view('livewire.customer.search-manager', [
            'searches' => $searches
        ])->layout('layouts.app');
    }

    public function checkJobStatus()
    {
        if ($this->recentlyCreatedSearchId && $this->searchJobStatus === 'searching') {
            // Check if any notifications were created for this search in the last 2 minutes
            $recentNotifications = Auth::user()->notifications()
                ->where('type', 'App\\Notifications\\SmartSearchMatch')
                ->where('data->smart_search_id', $this->recentlyCreatedSearchId)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->count();

            if ($recentNotifications > 0) {
                $this->searchJobStatus = 'completed';
                session()->flash('success',
                    "✅ Search completed! Found properties that match your search criteria. Check your notifications for details."
                );
                $this->recentlyCreatedSearchId = null;
            } elseif (now()->diffInMinutes(session('search_created_at', now())) > 1) {
                // If more than 1 minute has passed and no notifications, assume no matches
                $this->searchJobStatus = 'completed';
                session()->flash('success',
                    "✅ Search completed! No matches found at the moment. We'll notify you when new properties match your criteria based on your notification settings."
                );
                $this->recentlyCreatedSearchId = null;
            }
        }
    }

    public function onSearchJobCompleted($searchId, $success, $matchCount, $message)
    {
        if ($this->recentlyCreatedSearchId == $searchId) {
            $this->searchJobStatus = $success ? 'completed' : 'failed';
            session()->flash('success', $message);
            $this->recentlyCreatedSearchId = null;

            // Refresh the page data to show any new matches
            $this->resetPage();
        }
    }

    public function handleSearchTimeout()
    {
        if ($this->searchJobStatus === 'searching') {
            $this->searchJobStatus = 'completed';
            session()->flash('success',
                "✅ Search completed! We'll notify you when new properties match your criteria based on your notification settings."
            );
            $this->recentlyCreatedSearchId = null;
        }
    }

    public function deleteSearch($searchId)
    {
        $search = Auth::user()->smartSearches()->find($searchId);

        if ($search) {
            $search->delete();
            session()->flash('success', 'SmartSearch deleted successfully!');
        }
    }

    public function toggleSearchStatus($searchId)
    {
        $search = Auth::user()->smartSearches()->find($searchId);

        if ($search) {
            $search->update(['is_active' => !$search->is_active]);
            $status = $search->is_active ? 'activated' : 'deactivated';
            session()->flash('success', "SmartSearch {$status} successfully!");
        }
    }

    public function setDefaultSearch($searchId)
    {
        // First, remove default from all user's searches
        Auth::user()->smartSearches()->update(['is_default' => false]);

        // Set the selected search as default
        $search = Auth::user()->smartSearches()->find($searchId);
        if ($search) {
            $search->update(['is_default' => true]);
            session()->flash('success', 'Default SmartSearch updated successfully!');
        }
    }

    public function runSearch($searchId)
    {
        $search = Auth::user()->smartSearches()->find($searchId);

        if ($search) {
            // Dispatch the job for real-time progress tracking
            ProcessSmartSearchMatches::dispatch(null, $searchId, false);

            session()->flash('success', 'SmartSearch started! You\'ll see real-time progress updates as we scan for matching properties.');
        }
    }
}