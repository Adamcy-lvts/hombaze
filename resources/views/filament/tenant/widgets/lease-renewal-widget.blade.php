<x-filament-widgets::widget>
    @if($showWidget)
        <x-filament::section>
            @if($isExpired)
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-m-exclamation-triangle class="h-5 w-5 text-red-500" />
                        Lease Expired
                    </div>
                </x-slot>
            @else
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-m-clock class="h-5 w-5 text-amber-500" />
                        Lease Renewal Available
                    </div>
                </x-slot>
            @endif

            <div class="space-y-4">
                @if($isExpired)
                    <div class="rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-m-exclamation-triangle class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Your lease for {{ $lease->property->title }} has expired
                                </h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <p>Lease expired on {{ $lease->end_date->format('F j, Y') }}. Please contact your landlord to discuss renewal or extension options.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-md bg-amber-50 p-4 dark:bg-amber-900/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-m-clock class="h-5 w-5 text-amber-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                    Your lease expires in {{ $daysUntilExpiry }} days
                                </h3>
                                <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                                    <p>Lease for {{ $lease->property->title }} expires on {{ $lease->end_date->format('F j, Y') }}.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Current Lease Details</h4>
                        <dl class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex justify-between">
                                <dt>Property:</dt>
                                <dd class="font-medium">{{ $lease->property->title }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Monthly Rent:</dt>
                                <dd class="font-medium">₦{{ number_format($lease->monthly_rent, 0) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Lease Period:</dt>
                                <dd class="font-medium">
                                    {{ $lease->start_date->format('M j, Y') }} - {{ $lease->end_date->format('M j, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Renewal Options</h4>
                        <div class="mt-2">
                            @if($canRenew)
                                <div class="space-y-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Renewal is {{ $lease->renewal_option === 'automatic' ? 'automatic' : 'available' }}
                                    </p>
                                    @if($lease->renewal_option === 'landlord_approval')
                                        <p class="text-xs text-gray-400">Subject to landlord approval</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-red-600 dark:text-red-400">
                                    Renewal not available for this lease
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                @if($canRenew && !$isExpired)
                    @if($hasExistingRequest)
                        <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <x-heroicon-m-information-circle class="h-5 w-5 text-blue-400" />
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                        Renewal Request Submitted
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        <p>Your renewal request was submitted on {{ $existingRequest->created_at->format('F j, Y') }}. Status: {{ ucfirst($existingRequest->status) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3">
                        <x-filament::button
                            color="gray"
                            tag="a"
                            href="mailto:{{ $lease->property->owner->email ?? 'support@homebaze.com' }}"
                        >
                            Contact Landlord
                        </x-filament::button>
                        
                        @if(!$hasExistingRequest)
                            <x-filament::button
                                color="primary"
                                wire:click="requestRenewal"
                            >
                                Request Renewal
                            </x-filament::button>
                        @else
                            <x-filament::button
                                color="gray"
                                disabled
                            >
                                Request Submitted
                            </x-filament::button>
                        @endif
                    </div>
                @elseif($isExpired)
                    <div class="flex justify-end">
                        <x-filament::button
                            color="danger"
                            tag="a"
                            href="mailto:{{ $lease->property->owner->email ?? 'support@homebaze.com' }}"
                        >
                            Contact Landlord Immediately
                        </x-filament::button>
                    </div>
                @endif
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
