<?php

namespace App\Livewire\Customer;

use App\Models\SavedSearch;
use App\Jobs\ProcessSavedSearchMatches;
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
        $searches = Auth::user()->savedSearches()
            ->latest()
            ->paginate(10);

        // Get recent match notifications for each search
        $searchesWithMatches = $searches->getCollection()->map(function ($search) {
            // Get recent SavedSearchMatch notifications for this search
            $recentMatches = Auth::user()->notifications()
                ->where('type', 'App\\Notifications\\SavedSearchMatch')
                ->where('data->saved_search_id', $search->id)
                ->where('created_at', '>=', now()->subDays(7)) // Last 7 days
                ->latest()
                ->limit(3)
                ->get();

            $search->recent_matches = $recentMatches;
            $search->has_matches = $recentMatches->isNotEmpty();

            // Get the latest matched property if exists
            if ($search->has_matches) {
                $latestMatch = $recentMatches->first();
                $properties = $latestMatch->data['properties'] ?? [];
                $search->latest_matched_property = !empty($properties) ? $properties[0] : null;

                // Ensure the property has required fields
                if ($search->latest_matched_property &&
                    (!isset($search->latest_matched_property['slug']) ||
                     !isset($search->latest_matched_property['title']))) {
                    $search->latest_matched_property = null;
                }
            }

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
                ->where('type', 'App\\Notifications\\SavedSearchMatch')
                ->where('data->saved_search_id', $this->recentlyCreatedSearchId)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->count();

            if ($recentNotifications > 0) {
                $this->searchJobStatus = 'completed';
                session()->flash('success',
                    "✅ Search completed! Found {$recentNotifications} matching " .
                    ($recentNotifications === 1 ? 'property' : 'properties') .
                    ". Check your notifications for details."
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
        $search = Auth::user()->savedSearches()->find($searchId);

        if ($search) {
            $search->delete();
            session()->flash('success', 'Search deleted successfully!');
        }
    }

    public function toggleSearchStatus($searchId)
    {
        $search = Auth::user()->savedSearches()->find($searchId);

        if ($search) {
            $search->update(['is_active' => !$search->is_active]);
            $status = $search->is_active ? 'activated' : 'deactivated';
            session()->flash('success', "Search {$status} successfully!");
        }
    }

    public function setDefaultSearch($searchId)
    {
        // First, remove default from all user's searches
        Auth::user()->savedSearches()->update(['is_default' => false]);

        // Set the selected search as default
        $search = Auth::user()->savedSearches()->find($searchId);
        if ($search) {
            $search->update(['is_default' => true]);
            session()->flash('success', 'Default search updated successfully!');
        }
    }

    public function runSearch($searchId)
    {
        $search = Auth::user()->savedSearches()->find($searchId);

        if ($search) {
            // Dispatch the job for real-time progress tracking
            ProcessSavedSearchMatches::dispatch(null, $searchId, false);

            session()->flash('success', 'Search started! You\'ll see real-time progress updates as we scan for matching properties.');
        }
    }
}