<x-filament-widgets::widget>
    <x-filament::section>
        @if(!$this->getViewData()['hasProfile'])
            <!-- No Profile Created -->
            <div class="p-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                        <x-heroicon-o-user-circle class="w-8 h-8 text-orange-600" />
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Complete Your Profile
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Create your property owner profile to start managing your properties effectively and receive personalized services.
                </p>

                <div class="flex justify-center">
                    <x-filament::button
                        href="{{ \App\Filament\Landlord\Resources\PropertyOwnerResource::getUrl('create') }}"
                        color="primary"
                        icon="heroicon-o-user-plus"
                    >
                        Create My Profile
                    </x-filament::button>
                </div>
            </div>
        @else
            @php
                $data = $this->getViewData();
                $completionPercentage = $data['completionPercentage'];
                $isComplete = $data['isComplete'];
                $missingFields = $data['missingFields'];
                $propertyOwner = $data['propertyOwner'];
            @endphp

            <!-- Profile Completion -->
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        @if($propertyOwner->profile_photo)
                            <img src="{{ Storage::url($propertyOwner->profile_photo) }}"
                                 alt="Profile Photo"
                                 class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                <x-heroicon-o-user class="w-6 h-6 text-gray-500" />
                            </div>
                        @endif

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Welcome, {{ $propertyOwner->name }}!
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ ucfirst($propertyOwner->type) }} Owner
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-2xl font-bold {{ $isComplete ? 'text-green-600' : 'text-orange-600' }}">
                            {{ $completionPercentage }}%
                        </div>
                        <p class="text-xs text-gray-500">Profile Complete</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile Completion</span>
                        <span class="text-sm text-gray-500">{{ $completionPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-300 {{ $isComplete ? 'bg-green-500' : 'bg-orange-500' }}"
                             style="width: {{ $completionPercentage }}%"></div>
                    </div>
                </div>

                @if(!$isComplete && count($missingFields) > 0)
                    <!-- Missing Fields -->
                    <div class="mb-4 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                        <h4 class="text-sm font-medium text-orange-800 dark:text-orange-200 mb-2">
                            Complete your profile to unlock all features:
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($missingFields as $field)
                                <span class="inline-flex items-center px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs font-medium rounded-full">
                                    {{ $field }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @elseif($isComplete)
                    <!-- Profile Complete -->
                    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex items-center">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 mr-2" />
                            <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                Your profile is complete! You can now access all platform features.
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <x-filament::button
                        href="{{ \App\Filament\Landlord\Resources\PropertyOwnerResource::getUrl('edit', ['record' => $propertyOwner]) }}"
                        color="primary"
                        icon="heroicon-o-pencil"
                        size="sm"
                    >
                        Edit Profile
                    </x-filament::button>

                    <x-filament::button
                        href="{{ \App\Filament\Landlord\Resources\PropertyResource::getUrl('index') }}"
                        color="gray"
                        icon="heroicon-o-home"
                        size="sm"
                    >
                        My Properties
                    </x-filament::button>

                    @if($propertyOwner->is_verified)
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">
                            <x-heroicon-o-shield-check class="w-4 h-4 mr-1" />
                            Verified
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-medium rounded-full">
                            <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                            Pending Verification
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>