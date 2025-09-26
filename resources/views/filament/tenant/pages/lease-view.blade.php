{{-- resources/views/filament/tenant/pages/lease-view.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
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
                                    <p class="mt-1 text-xs text-blue-600">This lease uses your landlord's custom template</p>
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
                                    <h3 class="text-lg font-medium text-gray-800">Standard Lease Agreement</h3>
                                    <p class="mt-1 text-sm text-gray-700">This lease uses the standard HomeBaze template</p>
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
                        <p>Document loaded on {{ $this->leaseDocument['generated_at']->format('F j, Y \a\t g:i A') }}</p>
                        @if($this->leaseDocument['template'])
                            <p>Using landlord's template: {{ $this->leaseDocument['template']->name }}</p>
                        @else
                            <p>Using standard template</p>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                <p class="text-yellow-700">Loading your lease document...</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>