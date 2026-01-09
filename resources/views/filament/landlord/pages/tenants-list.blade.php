<x-filament-panels::page>
    <div class="pb-20">
        {{-- Header & Search --}}
        <div class="mb-6 space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight text-white">Tenants</h1>
                <a href="{{ route('filament.landlord.resources.tenants.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors border border-transparent rounded-lg shadow-sm bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    New tenant
                </a>
            </div>

            <div class="flex space-x-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                    </div>
                    <input type="search" 
                           wire:model.live.debounce.300ms="search"
                           class="block w-full p-3 pl-10 text-sm text-white placeholder-gray-400 bg-gray-800 border-gray-700 rounded-xl focus:border-primary-500 focus:ring-primary-500" 
                           placeholder="Search">
                </div>
                <button class="p-3 text-gray-400 bg-gray-800 border border-gray-700 rounded-xl hover:text-white hover:bg-gray-700">
                    <x-heroicon-o-funnel class="w-5 h-5" />
                </button>
                 <button class="p-3 text-gray-400 bg-gray-800 border border-gray-700 rounded-xl hover:text-white hover:bg-gray-700">
                    <x-heroicon-o-view-columns class="w-5 h-5" />
                </button>
            </div>
        </div>

        {{-- Tenants List --}}
        <div class="space-y-4">
            @forelse($this->tenants as $tenant)
                <div wire:click="viewTenant({{ $tenant->id }})" class="relative flex items-center p-4 transition-all border border-gray-800 bg-gray-900 rounded-2xl hover:bg-gray-800/80 group active:scale-[0.99] cursor-pointer">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0 mr-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($tenant->first_name . ' ' . $tenant->last_name) }}&color=7F9CF5&background=EBF4FF" 
                             alt="{{ $tenant->first_name }}" 
                             class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-800 group-hover:ring-primary-500/50 transition-all">
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-base font-semibold text-white truncate">
                                {{ $tenant->first_name }} {{ $tenant->last_name }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $tenant->is_active ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="flex flex-col space-y-0.5">
                            @if($tenant->email)
                            <div class="flex items-center text-xs text-gray-400">
                                <x-heroicon-m-envelope class="w-3 h-3 mr-1.5 flex-shrink-0"/>
                                <span class="truncate">{{ $tenant->email }}</span>
                            </div>
                            @endif
                            
                            @if($tenant->phone)
                            <div class="flex items-center text-xs text-gray-400">
                                <x-heroicon-m-phone class="w-3 h-3 mr-1.5 flex-shrink-0"/>
                                <span>{{ $tenant->phone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Chevron (Visual Cue) --}}
                     <div class="ml-2 text-gray-600 group-hover:text-gray-400 transition-colors">
                        <x-heroicon-m-chevron-right class="w-5 h-5"/>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="p-4 mb-4 bg-gray-800 rounded-full">
                        <x-heroicon-o-users class="w-8 h-8 text-gray-400" />
                    </div>
                    <p class="text-lg font-medium text-white">No tenants found</p>
                    <p class="text-sm text-gray-400">Try adjusting your search or add a new tenant.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $this->tenants->links() }}
        </div>
        
        {{-- Tenant Detail Modal --}}
        @if($selectedTenant)
            <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center bg-black/80 backdrop-blur-sm p-4 sm:p-0"
                 x-data
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="w-full max-w-lg bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6 relative"
                     @click.away="$wire.closeTenantView()">
                    
                    {{-- Close Button --}}
                    <button wire:click="closeTenantView" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                        <x-heroicon-o-x-mark class="w-6 h-6"/>
                    </button>

                    <div class="text-center mb-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedTenant->first_name . ' ' . $selectedTenant->last_name) }}&color=7F9CF5&background=EBF4FF" 
                             class="w-20 h-20 rounded-full mx-auto mb-4 object-cover ring-4 ring-gray-800">
                        <h2 class="text-2xl font-bold text-white">{{ $selectedTenant->first_name }} {{ $selectedTenant->last_name }}</h2>
                        <p class="text-gray-400">{{ $selectedTenant->email }}</p>
                        <p class="text-gray-400">{{ $selectedTenant->phone }}</p>
                    </div>

                    {{-- Active Lease Section --}}
                    @php
                        $activeLease = $selectedTenant->currentLease();
                    @endphp

                    @if($activeLease && $activeLease->property)
                        {{-- Property Details --}}
                        <div class="mb-4 bg-gray-800 border border-gray-700 rounded-xl p-4">
                            <h3 class="mb-3 text-xs font-medium tracking-wider text-gray-400 uppercase">Property Details</h3>
                            
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-base font-semibold text-white">{{ $activeLease->property->title }}</h4>
                                    <p class="text-sm text-gray-400">{{ $activeLease->property->address }}</p>
                                    @if($activeLease->property->area)
                                        <p class="text-sm text-gray-500">{{ $activeLease->property->area->name }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-base font-bold text-primary-400">
                                        {{ $activeLease->property->formatted_price }}
                                    </p>
                                    <p class="text-xs text-gray-500">/year</p>
                                </div>
                            </div>
                        </div>

                         @php
                            $start = \Carbon\Carbon::parse($activeLease->start_date);
                            $end = \Carbon\Carbon::parse($activeLease->end_date);
                            $now = \Carbon\Carbon::now();
                            $totalDays = $start->diffInDays($end) ?: 1; // Avoid division by zero
                            $remainingDays = max(0, $now->diffInDays($end, false));
                            
                            // "Dropping from 100 to 0" means we show percentage remaining
                            // If now < start, it's 100%. If now > end, it's 0%.
                            $percentage = 0;
                            if ($now->lt($start)) {
                                $percentage = 100;
                            } elseif ($now->gt($end)) {
                                $percentage = 0;
                            } else {
                                $percentage = ($remainingDays / $totalDays) * 100;
                            }
                        @endphp

                        <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                            <h3 class="text-sm font-medium text-gray-300 mb-3 uppercase tracking-wider">Current Lease</h3>
                            
                            <div class="flex justify-between text-sm text-gray-200 mb-2">
                                <span>{{ $start->format('d M Y') }}</span>
                                <span>{{ $end->format('d M Y') }}</span>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="w-full bg-gray-700 rounded-full h-2.5 mb-2 overflow-hidden">
                                <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                            
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-400">{{ round($remainingDays) }} days remaining</span>
                                <span class="text-primary-400 font-medium">{{ round($percentage) }}%</span>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 text-center">
                             <h3 class="text-sm font-medium text-gray-300 mb-2 uppercase tracking-wider">Current Lease</h3>
                             <p class="text-gray-500">No active lease found.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
