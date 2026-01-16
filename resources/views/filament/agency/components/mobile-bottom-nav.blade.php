@auth
    @php
        $tenant = \Filament\Facades\Filament::getTenant();
        $tenantSlug = $tenant?->slug ?? '';
        
        $items = [
            [
                'label' => 'Home',
                'icon' => 'heroicon-o-home',
                'activeIcon' => 'heroicon-s-home',
                'url' => route('filament.agency.pages.agency-dashboard', ['tenant' => $tenantSlug]),
                'active' => request()->routeIs('filament.agency.pages.agency-dashboard'),
            ],
            [
                'label' => 'Properties',
                'icon' => 'heroicon-o-building-office-2',
                'activeIcon' => 'heroicon-s-building-office-2',
                'url' => route('filament.agency.pages.my-properties', ['tenant' => $tenantSlug]),
                'active' => request()->routeIs('filament.agency.pages.my-properties') 
                    || request()->routeIs('filament.agency.pages.edit-property') 
                    || request()->routeIs('filament.agency.resources.properties.*'),
            ],
            [
                'route' => 'create',
                'label' => 'Create',
                'icon' => 'heroicon-s-plus',
                'url' => route('filament.agency.pages.create-property', ['tenant' => $tenantSlug]),
                'active' => request()->routeIs('filament.agency.pages.create-property'),
            ],
            [
                'label' => 'Agents',
                'icon' => 'heroicon-o-user-group',
                'activeIcon' => 'heroicon-s-user-group',
                'url' => route('filament.agency.pages.agents-list', ['tenant' => $tenantSlug]),
                'active' => request()->routeIs('filament.agency.pages.agents-list') 
                    || request()->routeIs('filament.agency.resources.agents.*'),
            ],
            [
                'label' => 'Leads',
                'icon' => 'heroicon-o-chat-bubble-bottom-center-text',
                'activeIcon' => 'heroicon-s-chat-bubble-bottom-center-text',
                'url' => route('filament.agency.pages.inquiries', ['tenant' => $tenantSlug]),
                'active' => request()->routeIs('filament.agency.pages.inquiries') 
                    || request()->routeIs('filament.agency.resources.property-inquiries.*'),
            ],
        ];
    @endphp

    <div class="fixed bottom-0 left-0 right-0 z-[9999] md:hidden pb-safe">
        <div class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] flex justify-between items-end px-2 h-16">
            
            @foreach($items as $item)
                @if(isset($item['route']) && $item['route'] === 'create')
                    {{-- Center Create Button --}}
                    <div class="relative -top-5 flex justify-center">
                        <a href="{{ $item['url'] }}" 
                           class="flex items-center justify-center w-14 h-14 rounded-full bg-primary-600 text-white shadow-lg shadow-primary-500/50 hover:bg-primary-500 transition-all transform active:scale-95">
                            <x-filament::icon
                                icon="{{ $item['icon'] }}"
                                class="h-8 w-8"
                            />
                        </a>
                    </div>
                @else
                    <a href="{{ $item['url'] }}" 
                       class="flex-1 flex flex-col items-center justify-center h-full py-2 space-y-1 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-500 {{ $item['active'] ? 'text-primary-600 dark:text-primary-500' : '' }}">
                        <x-filament::icon
                            icon="{{ $item['active'] ? $item['activeIcon'] : $item['icon'] }}"
                            class="h-6 w-6 {{ $item['active'] ? 'animate-bounce-subtle' : '' }}"
                        />
                        <span class="text-[10px] font-medium leading-none">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <style>
        /* Safe area padding for iPhones without home button */
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 20px);
        }
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 0.3s ease-in-out;
        }
        
        /* Force fixed positioning cleanup */
        body { 
            margin-bottom: 0 !important; 
        }
    </style>
@endauth
