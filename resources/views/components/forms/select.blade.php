@props([
    'label' => '',
    'placeholder' => 'Select an option',
    'required' => false,
    'disabled' => false,
    'error' => '',
    'hint' => '',
    'options' => [],
    'selected' => null,
    'searchable' => true,
])

@php
    $dropdownId = 'dropdown-' . uniqid();
    $inputId = 'input-' . uniqid();
    $searchId = 'search-' . uniqid();
    $optionsArray = [];

    if($options instanceof \Illuminate\Support\Collection) {
        $optionsArray = $options->map(fn($option) => ['value' => $option->id, 'label' => $option->name])->toArray();
    } elseif(is_array($options)) {
        $optionsArray = collect($options)->map(fn($label, $value) => ['value' => $value, 'label' => $label])->values()->toArray();
    }

    $selectedLabel = '';
    if($selected && $optionsArray) {
        $selectedOption = collect($optionsArray)->firstWhere('value', $selected);
        $selectedLabel = $selectedOption['label'] ?? '';
    }
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-900">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative" id="{{ $dropdownId }}">
        <!-- Hidden input for form submission -->
        <input
            type="hidden"
            {{ $attributes->except(['label', 'placeholder', 'required', 'disabled', 'error', 'hint', 'options', 'selected', 'searchable', 'class']) }}
            value="{{ $selected }}"
            id="{{ $inputId }}"
        />

        <!-- Additional input for Livewire compatibility -->
        <input
            type="text"
            style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;"
            {{ $attributes->only(['wire:model', 'wire:model.live', 'wire:model.defer']) }}
            value="{{ $selected }}"
            id="{{ $inputId }}-livewire"
        />

        <!-- Display button -->
        <button
            type="button"
            onclick="toggleDropdown('{{ $dropdownId }}')"
            @if($disabled) disabled @endif
            class="block w-full px-4 py-3 text-left text-gray-900 border border-gray-300/60 rounded-xl shadow-xs bg-white/95 backdrop-blur-xl transition-all duration-300 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 hover:border-gray-400/60 hover:shadow-md disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed cursor-pointer{{ $error ? ' border-red-300 focus:border-red-500 focus:ring-red-500/50' : '' }}"
        >
            <span class="block truncate" id="{{ $dropdownId }}-selected">
                @if($selectedLabel)
                    {{ $selectedLabel }}
                @else
                    <span class="text-gray-500">{{ $placeholder }}</span>
                @endif
            </span>
        </button>

        <!-- Dropdown arrow -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400 transition-transform duration-200" id="{{ $dropdownId }}-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Clear button -->
        @if($selected)
            <div
                onclick="clearSelection('{{ $dropdownId }}', '{{ $inputId }}', '{{ $placeholder }}')"
                class="absolute inset-y-0 right-8 flex items-center pr-3 cursor-pointer"
            >
                <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        @endif

        <!-- Dropdown options -->
        <div
            id="{{ $dropdownId }}-options"
            class="absolute z-9999 w-full mt-1 bg-white border border-gray-300/60 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden"
        >
            <div class="py-1">
                @if($searchable && count($optionsArray) > 5)
                    <!-- Search input -->
                    <div class="px-3 py-2 border-b border-gray-200">
                        <input
                            type="text"
                            id="{{ $searchId }}"
                            placeholder="Search options..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500"
                            onkeyup="filterOptions('{{ $dropdownId }}', this.value)"
                        />
                    </div>
                @endif

                <!-- No results message -->
                <div id="{{ $dropdownId }}-no-results" class="px-4 py-2 text-sm text-gray-500 hidden">
                    No options found
                </div>

                <!-- Options list -->
                <div id="{{ $dropdownId }}-list">
                    @foreach($optionsArray as $option)
                        <div
                            class="option-item px-4 py-2 text-sm cursor-pointer transition-colors duration-150 hover:bg-gray-50 {{ $selected == $option['value'] ? 'bg-emerald-50 text-emerald-900' : 'text-gray-900' }}"
                            onclick="selectOption('{{ $dropdownId }}', '{{ $inputId }}', '{{ $option['value'] }}', {{ json_encode($option['label']) }})"
                            data-value="{{ $option['value'] }}"
                            data-label="{{ htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') }}"
                        >
                            <span>{{ $option['label'] }}</span>
                            @if($selected == $option['value'])
                                <svg class="inline-block ml-2 h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-sm text-gray-500">{{ $hint }}</p>
    @endif
</div>

<script>
function toggleDropdown(dropdownId) {
    const optionsDiv = document.getElementById(dropdownId + '-options');
    const arrow = document.getElementById(dropdownId + '-arrow');
    const container = document.getElementById(dropdownId);

    if (optionsDiv.classList.contains('hidden')) {
        // Close all other dropdowns
        document.querySelectorAll('[id$="-options"]').forEach(el => {
            if (el.id !== dropdownId + '-options') {
                el.classList.add('hidden');
            }
        });
        document.querySelectorAll('[id$="-arrow"]').forEach(el => {
            if (el.id !== dropdownId + '-arrow') {
                el.classList.remove('rotate-180');
            }
        });

        // Check if dropdown should open upward
        const rect = container.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        const dropdownHeight = 240; // max-h-60 = 15rem = 240px
        const spaceBelow = viewportHeight - rect.bottom;

        if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
            // Position above
            optionsDiv.style.bottom = '100%';
            optionsDiv.style.top = 'auto';
            optionsDiv.style.marginBottom = '4px';
            optionsDiv.style.marginTop = '0';
        } else {
            // Position below (default)
            optionsDiv.style.top = '100%';
            optionsDiv.style.bottom = 'auto';
            optionsDiv.style.marginTop = '4px';
            optionsDiv.style.marginBottom = '0';
        }

        // Open this dropdown
        optionsDiv.classList.remove('hidden');
        arrow.classList.add('rotate-180');

        // Focus search if it exists
        const searchInput = optionsDiv.querySelector('input[type="text"]');
        if (searchInput) {
            setTimeout(() => searchInput.focus(), 100);
        }
    } else {
        optionsDiv.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

function selectOption(dropdownId, inputId, value, label) {
    const hiddenInput = document.getElementById(inputId);
    const livewireInput = document.getElementById(inputId + '-livewire');
    const selectedSpan = document.getElementById(dropdownId + '-selected');
    const optionsDiv = document.getElementById(dropdownId + '-options');
    const arrow = document.getElementById(dropdownId + '-arrow');

    // Update hidden input
    hiddenInput.value = value;

    // Update livewire input
    if (livewireInput) {
        livewireInput.value = value;
    }

    // Update display
    selectedSpan.innerHTML = label;

    // Update option highlighting
    optionsDiv.querySelectorAll('.option-item').forEach(item => {
        item.classList.remove('bg-emerald-50', 'text-emerald-900');
        item.classList.add('text-gray-900');
        item.querySelector('svg')?.remove();
    });

    const selectedItem = optionsDiv.querySelector(`[data-value="${value}"]`);
    if (selectedItem) {
        selectedItem.classList.add('bg-emerald-50', 'text-emerald-900');
        selectedItem.classList.remove('text-gray-900');
        selectedItem.innerHTML += '<svg class="inline-block ml-2 h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
    }

    // Close dropdown
    optionsDiv.classList.add('hidden');
    arrow.classList.remove('rotate-180');

    // Trigger change event for Livewire (simplified approach)
    if (livewireInput) {
        livewireInput.dispatchEvent(new Event('input', { bubbles: true }));
    } else {
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function clearSelection(dropdownId, inputId, placeholder) {
    const hiddenInput = document.getElementById(inputId);
    const livewireInput = document.getElementById(inputId + '-livewire');
    const selectedSpan = document.getElementById(dropdownId + '-selected');
    const optionsDiv = document.getElementById(dropdownId + '-options');

    // Clear hidden input
    hiddenInput.value = '';

    // Clear livewire input
    if (livewireInput) {
        livewireInput.value = '';
    }

    // Update display
    selectedSpan.innerHTML = `<span class="text-gray-500">${placeholder}</span>`;

    // Clear option highlighting
    optionsDiv.querySelectorAll('.option-item').forEach(item => {
        item.classList.remove('bg-emerald-50', 'text-emerald-900');
        item.classList.add('text-gray-900');
        item.querySelector('svg')?.remove();
    });

    // Trigger change event for Livewire (simplified approach)
    if (livewireInput) {
        livewireInput.dispatchEvent(new Event('input', { bubbles: true }));
    } else {
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function filterOptions(dropdownId, searchValue) {
    const optionsList = document.getElementById(dropdownId + '-list');
    const noResults = document.getElementById(dropdownId + '-no-results');
    const options = optionsList.querySelectorAll('.option-item');

    let visibleCount = 0;

    options.forEach(option => {
        try {
            const label = (option.getAttribute('data-label') || '').toLowerCase();
            const searchTerm = (searchValue || '').toLowerCase();
            const matches = label.includes(searchTerm);

            if (matches) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        } catch (error) {
            // Fallback: show the option if there's an error
            option.style.display = 'block';
            visibleCount++;
        }
    });

    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id$="-options"]').forEach(el => {
            el.classList.add('hidden');
        });
        document.querySelectorAll('[id$="-arrow"]').forEach(el => {
            el.classList.remove('rotate-180');
        });
    }
});

// Close dropdowns on scroll and resize
window.addEventListener('scroll', function() {
    document.querySelectorAll('[id$="-options"]').forEach(el => {
        if (!el.classList.contains('hidden')) {
            el.classList.add('hidden');
        }
    });
    document.querySelectorAll('[id$="-arrow"]').forEach(el => {
        el.classList.remove('rotate-180');
    });
});

window.addEventListener('resize', function() {
    document.querySelectorAll('[id$="-options"]').forEach(el => {
        if (!el.classList.contains('hidden')) {
            el.classList.add('hidden');
        }
    });
    document.querySelectorAll('[id$="-arrow"]').forEach(el => {
        el.classList.remove('rotate-180');
    });
});

// Function to restore select state after Livewire updates
function restoreSelectStates() {
    // Find all select components and restore their display state
    document.querySelectorAll('[id^="dropdown-"]').forEach(container => {
        const dropdownId = container.id;
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const selectedSpan = container.querySelector('[id$="-selected"]');
        const optionsDiv = container.querySelector('[id$="-options"]');

        if (hiddenInput && selectedSpan && optionsDiv && hiddenInput.value) {
            // Find the option with the current value
            const selectedOption = optionsDiv.querySelector(`[data-value="${hiddenInput.value}"]`);
            if (selectedOption) {
                const label = selectedOption.getAttribute('data-label');

                // Update display
                selectedSpan.innerHTML = label;

                // Update option highlighting
                optionsDiv.querySelectorAll('.option-item').forEach(item => {
                    item.classList.remove('bg-emerald-50', 'text-emerald-900');
                    item.classList.add('text-gray-900');
                    item.querySelector('svg')?.remove();
                });

                selectedOption.classList.add('bg-emerald-50', 'text-emerald-900');
                selectedOption.classList.remove('text-gray-900');
                if (!selectedOption.querySelector('svg')) {
                    selectedOption.innerHTML += '<svg class="inline-block ml-2 h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
                }
            }
        }
    });
}

// Restore state on page load
document.addEventListener('DOMContentLoaded', restoreSelectStates);

// Restore state after Livewire updates (try multiple event names for different Livewire versions)
document.addEventListener('livewire:navigated', restoreSelectStates);
document.addEventListener('livewire:load', restoreSelectStates);
document.addEventListener('livewire:update', restoreSelectStates);
document.addEventListener('livewire:morph', restoreSelectStates);
document.addEventListener('livewire:updated', restoreSelectStates);

// Also use MutationObserver as fallback for DOM changes
if (window.MutationObserver) {
    const observer = new MutationObserver(function(mutations) {
        let shouldRestore = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Check if any added nodes contain select components
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && (node.querySelector('[id^="dropdown-"]') || node.matches('[id^="dropdown-"]'))) {
                        shouldRestore = true;
                    }
                });
            }
        });

        if (shouldRestore) {
            setTimeout(restoreSelectStates, 50); // Small delay to ensure DOM is fully updated
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
</script>