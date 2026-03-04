<x-filament-panels::page>
    <div class="space-y-6">
        @if($createdReceipt)
            {{-- Success State --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-check-circle class="w-10 h-10 text-green-600 dark:text-green-400" />
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Receipt Created Successfully!</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Receipt #{{ $createdReceipt->receipt_number }}</p>
                
                <div class="flex flex-wrap justify-center gap-3">
                    <x-filament::button color="primary" wire:click="viewReceipt" icon="heroicon-o-eye">
                        View Receipt
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="createAnother" icon="heroicon-o-plus">
                        Create Another
                    </x-filament::button>
                </div>
            </div>
        @else
            <x-filament-panels::form wire:submit="create">
                {{ $this->form }}
                
                <div class="flex justify-end mt-6">
                    <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                        Create Receipt
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        @endif
    </div>
</x-filament-panels::page>
