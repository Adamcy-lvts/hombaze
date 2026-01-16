@auth
@php
    $panelId = filament()->getCurrentPanel()->getId();
    $items = [];

    if ($panelId === 'landlord') {
        $items = [
            [
                'label' => 'Home',
                'icon' => 'heroicon-o-home',
                'activeIcon' => 'heroicon-s-home',
                'url' => route('filament.landlord.pages.dashboard'),
                'active' => request()->routeIs('filament.landlord.pages.dashboard'),
            ],
            [
                'label' => 'Properties',
                'icon' => 'heroicon-o-building-office-2',
                'activeIcon' => 'heroicon-s-building-office-2',
                // Link to custom mobile list page
                'url' => route('filament.landlord.pages.my-properties'),
                // Active if on custom list OR standard resource pages (like Edit)
                'active' => request()->routeIs('filament.landlord.pages.my-properties') || request()->routeIs('filament.landlord.resources.properties.*'),
            ],
            [
                'route' => 'create',
                'label' => 'Create',
                'icon' => 'heroicon-s-plus',
                'url' => route('filament.landlord.pages.create-property'),
                'active' => request()->routeIs('filament.landlord.pages.create-property'),
            ],
            [
                'label' => 'Tenants',
                'icon' => 'heroicon-o-users',
                'activeIcon' => 'heroicon-s-users',
                // Link to custom mobile list page
                'url' => route('filament.landlord.pages.tenants-list'),
                'active' => request()->routeIs('filament.landlord.pages.tenants-list') || request()->routeIs('filament.landlord.resources.tenants.*'),
            ],
            [
                'label' => 'Invitations',
                'icon' => 'heroicon-o-paper-airplane',
                'activeIcon' => 'heroicon-s-paper-airplane',
                'url' => route('filament.landlord.pages.invite-tenant'),
                'active' => request()->routeIs('filament.landlord.pages.invite-tenant'),
            ],
        ];
    } elseif ($panelId === 'agent') {
        $items = [
            [
                'label' => 'Home',
                'icon' => 'heroicon-o-home',
                'activeIcon' => 'heroicon-s-home',
                'url' => route('filament.agent.pages.dashboard'),
                'active' => request()->routeIs('filament.agent.pages.dashboard'),
            ],
            [
                'label' => 'Properties',
                'icon' => 'heroicon-o-building-office-2',
                'activeIcon' => 'heroicon-s-building-office-2',
                'url' => route('filament.agent.pages.my-properties'),
                'active' => request()->routeIs('filament.agent.pages.my-properties') || request()->routeIs('filament.agent.pages.edit-property') || request()->routeIs('filament.agent.resources.properties.*'),
            ],
            [
                'route' => 'create',
                'label' => 'Create',
                'icon' => 'heroicon-s-plus',
                'url' => route('filament.agent.pages.create-property'),
                'active' => request()->routeIs('filament.agent.pages.create-property'),
            ],
            [
                'label' => 'Leads',
                'icon' => 'heroicon-o-chat-bubble-bottom-center-text',
                'activeIcon' => 'heroicon-s-chat-bubble-bottom-center-text',
                'url' => route('filament.agent.resources.property-inquiries.index'),
                'active' => request()->routeIs('filament.agent.resources.property-inquiries.*'),
            ],
            [
                'label' => 'Leases',
                'icon' => 'heroicon-o-document-text',
                'activeIcon' => 'heroicon-s-document-text',
                'url' => route('filament.agent.pages.mobile-leases'),
                'active' => request()->routeIs('filament.agent.pages.mobile-leases'),
            ],
        ];
    }
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
</style>
@endauth
