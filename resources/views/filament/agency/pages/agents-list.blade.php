<x-filament-panels::page>
    <div class="pb-20">
        {{-- Header & Search --}}
        <div class="mb-6 space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Agents</h1>
                <a href="{{ route('filament.agency.resources.agents.create', ['tenant' => \Filament\Facades\Filament::getTenant()?->slug]) }}" 
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors border border-transparent rounded-lg shadow-sm bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Add Agent
                </a>
            </div>

            <div class="flex space-x-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                    </div>
                    <input type="search" 
                           wire:model.live.debounce.300ms="search"
                           class="block w-full p-3 pl-10 text-sm text-gray-900 dark:text-white placeholder-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary-500 focus:ring-primary-500" 
                           placeholder="Search agents...">
                </div>
                <button class="p-3 text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:text-gray-600 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                    <x-heroicon-o-funnel class="w-5 h-5" />
                </button>
            </div>
        </div>

        {{-- Agents List --}}
        <div class="space-y-4">
            @forelse($this->agents as $agent)
                <div wire:click="viewAgent({{ $agent->id }})" class="relative flex items-center p-4 transition-all border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/80 group active:scale-[0.99] cursor-pointer">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0 mr-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($agent->user?->name ?? 'Agent') }}&color=7F9CF5&background=EBF4FF" 
                             alt="{{ $agent->user?->name }}" 
                             class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-800 group-hover:ring-primary-500/50 transition-all">
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                {{ $agent->user?->name ?? 'Unknown Agent' }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $agent->is_active ? 'bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' }}">
                                {{ $agent->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="flex flex-col space-y-0.5">
                            @if($agent->user?->email)
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <x-heroicon-m-envelope class="w-3 h-3 mr-1.5 flex-shrink-0"/>
                                <span class="truncate">{{ $agent->user->email }}</span>
                            </div>
                            @endif
                            
                            @if($agent->phone)
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <x-heroicon-m-phone class="w-3 h-3 mr-1.5 flex-shrink-0"/>
                                <span>{{ $agent->phone }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Stats Row --}}
                        <div class="flex items-center gap-3 mt-2">
                            <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <x-heroicon-m-building-office-2 class="w-3 h-3 mr-1"/>
                                {{ $agent->properties->count() }} Properties
                            </span>
                        </div>
                    </div>

                    {{-- Chevron --}}
                    <div class="ml-2 text-gray-400 dark:text-gray-600 group-hover:text-gray-500 dark:group-hover:text-gray-400 transition-colors">
                        <x-heroicon-m-chevron-right class="w-5 h-5"/>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="p-4 mb-4 bg-gray-100 dark:bg-gray-800 rounded-full">
                        <x-heroicon-o-user-group class="w-8 h-8 text-gray-400" />
                    </div>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">No agents found</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or add a new agent.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $this->agents->links() }}
        </div>
        
        {{-- Agent Detail Modal --}}
        @if($selectedAgent)
            <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center bg-black/80 backdrop-blur-sm p-4 sm:p-0"
                 x-data
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="w-full max-w-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl p-6 relative"
                     @click.away="$wire.closeAgentView()">
                    
                    {{-- Close Button --}}
                    <button wire:click="closeAgentView" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <x-heroicon-o-x-mark class="w-6 h-6"/>
                    </button>

                    <div class="text-center mb-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedAgent->user?->name ?? 'Agent') }}&color=7F9CF5&background=EBF4FF" 
                             class="w-20 h-20 rounded-full mx-auto mb-4 object-cover ring-4 ring-gray-200 dark:ring-gray-800">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $selectedAgent->user?->name ?? 'Unknown Agent' }}</h2>
                        <p class="text-gray-500 dark:text-gray-400">{{ $selectedAgent->user?->email }}</p>
                        <p class="text-gray-500 dark:text-gray-400">{{ $selectedAgent->phone }}</p>
                    </div>

                    {{-- Stats Section --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $selectedAgent->properties->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Properties</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $selectedAgent->properties->where('status', 'available')->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available</p>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="grid grid-cols-2 gap-3">
                        @if($selectedAgent->phone)
                        <a href="tel:{{ $selectedAgent->phone }}" 
                           class="flex items-center justify-center gap-2 py-3 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl transition-colors">
                            <x-heroicon-m-phone class="w-4 h-4" />
                            Call
                        </a>
                        @endif
                        @if($selectedAgent->user?->email)
                        <a href="mailto:{{ $selectedAgent->user->email }}" 
                           class="flex items-center justify-center gap-2 py-3 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                            <x-heroicon-m-envelope class="w-4 h-4" />
                            Email
                        </a>
                        @endif
                    </div>

                    {{-- View Full Profile Link --}}
                    <div class="mt-4 text-center">
                        <a href="{{ route('filament.agency.resources.agents.edit', ['tenant' => \Filament\Facades\Filament::getTenant()?->slug, 'record' => $selectedAgent->id]) }}" 
                           class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                            View Full Profile →
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
