{{-- resources/views/filament/pages/sales-agreement-view.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Template Selection</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose a template to format the sales agreement or use default formatting</p>

            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="template-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sales Agreement Template:</label>
                    <select
                        id="template-select"
                        wire:model.live="data.template_id"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-xs focus:outline-hidden focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    >
                        <option value="">Use default template</option>
                        @foreach($this->availableTemplates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select a template to format the sales agreement</p>
                </div>
            </div>
        </div>

        <x-filament::button wire:click="generateSalesAgreementDocument" type="button" size="lg" color="primary">
            <span wire:loading.remove wire:target="generateSalesAgreementDocument">
                @if(filled($this->salesAgreementDocument))
                    Regenerate Sales Agreement
                @else
                    Generate Sales Agreement
                @endif
            </span>
            <span wire:loading wire:target="generateSalesAgreementDocument">
                Generating...
            </span>
        </x-filament::button>

        @if ($this->salesAgreementDocument)
            <div class="mt-4">
                @if (isset($this->salesAgreementDocument['error']))
                    <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                        <p class="text-red-700">{{ $this->salesAgreementDocument['error'] }}</p>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6 lg:p-8 relative overflow-hidden">
                        <div class="relative z-10 space-y-4 sm:space-y-6 lg:space-y-8">
                            @if($this->salesAgreementDocument['template'])
                                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
                                    <h3 class="text-lg font-medium text-blue-800">Template: {{ $this->salesAgreementDocument['template']->name }}</h3>
                                    @if($this->salesAgreementDocument['template']->description)
                                        <p class="mt-1 text-sm text-blue-700">{{ $this->salesAgreementDocument['template']->description }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="mb-6 p-4 bg-gray-50 border-l-4 border-gray-400 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-800">Default Template</h3>
                                    <p class="mt-1 text-sm text-gray-700">Using standard sales agreement formatting</p>
                                </div>
                            @endif

                            @include('sales-components.sections.header', ['agreement' => $this->record])
                            @include('sales-components.sections.property-info', ['property' => $this->record->property])
                            @include('sales-components.sections.parties-info', ['agreement' => $this->record])
                            @include('sales-components.sections.sale-terms', ['agreement' => $this->record])
                            @include('sales-components.sections.terms-conditions', ['content' => $this->salesAgreementDocument['content']])
                            @include('sales-components.sections.signature', ['agreement' => $this->record])
                        </div>
                    </div>

                    <div class="p-4 mt-4 bg-gray-50 rounded-lg text-sm text-gray-600">
                        <p>Generated on {{ $this->salesAgreementDocument['generated_at']->format('F j, Y \a\t g:i A') }}</p>
                        @if($this->salesAgreementDocument['template'])
                            <p>Using template: {{ $this->salesAgreementDocument['template']->name }}</p>
                        @else
                            <p>Using default template</p>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>
