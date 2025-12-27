<?php

namespace App\Livewire\Components;

use App\Services\PropertySearchEngine;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchBar extends Component
{
    public string $query = '';

    public bool $showSuggestions = false;

    public array $suggestions = [];

    public bool $compact = false;

    public ?string $placeholder = null;

    public bool $autoFocus = false;

    public int $selectedIndex = -1;

    protected PropertySearchEngine $searchEngine;

    public function boot(PropertySearchEngine $searchEngine): void
    {
        $this->searchEngine = $searchEngine;
    }

    public function mount(
        bool $compact = false,
        ?string $placeholder = null,
        bool $autoFocus = false,
        ?string $initialQuery = null,
    ): void {
        $this->compact = $compact;
        $this->placeholder = $placeholder ?? 'Search properties, locations...';
        $this->autoFocus = $autoFocus;
        $this->query = $initialQuery ?? '';
    }

    public function updatedQuery(): void
    {
        $this->selectedIndex = -1;

        if (strlen($this->query) >= 2) {
            $this->loadSuggestions();
            $this->showSuggestions = true;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function loadSuggestions(): void
    {
        $suggestions = $this->searchEngine->suggest($this->query);
        $this->suggestions = $suggestions->map(fn ($s) => $s->toArray())->toArray();
    }

    public function selectSuggestion(int $index): void
    {
        if (!isset($this->suggestions[$index])) {
            return;
        }

        $suggestion = $this->suggestions[$index];

        // If it's a property, go directly to it
        if ($suggestion['type'] === 'property' && isset($suggestion['meta']['slug'])) {
            $this->redirect(route('property.show', $suggestion['meta']['slug']));
            return;
        }

        // For locations or any other type, search with the text
        $this->query = $suggestion['text'];
        $this->search();
    }

    public function search(): void
    {
        $this->showSuggestions = false;

        if (empty(trim($this->query))) {
            $this->redirect(route('properties.search'));
            return;
        }

        $this->redirect(route('properties.search', ['q' => $this->query]));
    }

    public function hideSuggestions(): void
    {
        $this->showSuggestions = false;
    }

    public function showSuggestionsDropdown(): void
    {
        if (strlen($this->query) >= 2 && !empty($this->suggestions)) {
            $this->showSuggestions = true;
        }
    }

    public function navigateUp(): void
    {
        if ($this->selectedIndex > 0) {
            $this->selectedIndex--;
        } else {
            $this->selectedIndex = count($this->suggestions) - 1;
        }
    }

    public function navigateDown(): void
    {
        if ($this->selectedIndex < count($this->suggestions) - 1) {
            $this->selectedIndex++;
        } else {
            $this->selectedIndex = 0;
        }
    }

    public function handleEnter(): void
    {
        if ($this->selectedIndex >= 0 && isset($this->suggestions[$this->selectedIndex])) {
            $this->selectSuggestion($this->selectedIndex);
        } else {
            $this->search();
        }
    }

    #[On('clear-search')]
    public function clearSearch(): void
    {
        $this->query = '';
        $this->suggestions = [];
        $this->showSuggestions = false;
        $this->selectedIndex = -1;
    }

    public function render()
    {
        return view('livewire.components.search-bar');
    }
}
