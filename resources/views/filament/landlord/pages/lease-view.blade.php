{{-- resources/views/filament/landlord/pages/lease-view.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Template Selection Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Template Selection</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose a template to format the lease document or use default formatting</p>
            
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="template-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lease Template:</label>
                    <select 
                        id="template-select" 
                        wire:model.live="data.template_id"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    >
                        <option value="">Use default template</option>
                        @php
                            $templates = \App\Models\LeaseTemplate::where('landlord_id', auth()->id())
                                ->where('is_active', true)
                                ->get();
                        @endphp
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select a template to format the lease document</p>
                </div>
            </div>
        </div>

        <x-filament::button wire:click="generateLeaseDocument" type="button" size="lg" color="primary">
            <span wire:loading.remove wire:target="generateLeaseDocument">
                @if(filled($this->leaseDocument))
                    Regenerate Lease Document
                @else
                    Generate Lease Document
                @endif
            </span>
            <span wire:loading wire:target="generateLeaseDocument">
                Generating...
            </span>
        </x-filament::button>

        @if ($this->leaseDocument)
            <div class="mt-4">
                @if (isset($this->leaseDocument['error']))
                    <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                        <p class="text-red-700">{{ $this->leaseDocument['error'] }}</p>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6 lg:p-8 relative overflow-hidden">
                        <!-- Content with relative positioning and responsive design -->
                        <div class="relative z-10 space-y-4 sm:space-y-6 lg:space-y-8">
                            
                            @if($this->leaseDocument['template'])
                                {{-- Use template-based rendering --}}
                                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
                                    <h3 class="text-lg font-medium text-blue-800">Template: {{ $this->leaseDocument['template']->name }}</h3>
                                    @if($this->leaseDocument['template']->description)
                                        <p class="mt-1 text-sm text-blue-700">{{ $this->leaseDocument['template']->description }}</p>
                                    @endif
                                </div>
                                
                                {{-- Component-based Layout --}}
                                @include('lease-components.sections.header')
                                @include('lease-components.sections.property-info', ['property' => $this->record->property])
                                @include('lease-components.sections.parties-info', [
                                    'landlord' => $this->record->landlord,
                                    'tenant' => $this->record->tenant
                                ])
                                @include('lease-components.sections.lease-terms', ['lease' => $this->record])
                                @include('lease-components.sections.terms-conditions', ['content' => $this->leaseDocument['content']])
                                @include('lease-components.sections.signature', [
                                    'landlord' => $this->record->landlord,
                                    'tenant' => $this->record->tenant,
                                    'config' => ['show_witness' => false]
                                ])
                                
                            @else
                                {{-- Use default rendering --}}
                                <div class="mb-6 p-4 bg-gray-50 border-l-4 border-gray-400 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-800">Default Template</h3>
                                    <p class="mt-1 text-sm text-gray-700">Using standard lease formatting</p>
                                </div>
                                
                                {{-- Component-based Layout for Default --}}
                                @include('lease-components.sections.header')
                                @include('lease-components.sections.property-info', ['property' => $this->record->property])
                                @include('lease-components.sections.parties-info', [
                                    'landlord' => $this->record->landlord,
                                    'tenant' => $this->record->tenant
                                ])
                                @include('lease-components.sections.lease-terms', ['lease' => $this->record])
                                @include('lease-components.sections.terms-conditions', ['content' => $this->leaseDocument['content']])
                                @include('lease-components.sections.signature', [
                                    'landlord' => $this->record->landlord,
                                    'tenant' => $this->record->tenant,
                                    'config' => ['show_witness' => false]
                                ])
                            @endif
                        </div>
                    </div>

                    <div class="p-4 mt-4 bg-gray-50 rounded-lg text-sm text-gray-600">
                        <p>Generated on {{ $this->leaseDocument['generated_at']->format('F j, Y \a\t g:i A') }}</p>
                        @if($this->leaseDocument['template'])
                            <p>Using template: {{ $this->leaseDocument['template']->name }}</p>
                        @else
                            <p>Using default template</p>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>