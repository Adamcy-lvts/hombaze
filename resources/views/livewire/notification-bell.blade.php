<div class="relative"
     x-data="{ showDropdown: @entangle('showDropdown'), listening: false }"
     x-init="
        if (window.Echo && !listening) {
            listening = true;
            window.Echo.private('user.{{ Auth::id() }}')
                .notification((notification) => {
                    // Refresh notifications when a new one is received
                    $wire.refreshNotifications();
                });
        }
     ">
    <!-- Notification Bell Button -->
    <button
        wire:click="toggleDropdown"
        class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full transition ease-in-out duration-150"
        :class="showDropdown ? 'text-gray-700 bg-gray-100' : ''"
    >
        <!-- Bell Icon -->
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Notification Badge -->
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-4 h-4 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Backdrop Overlay -->
    <div
        x-show="showDropdown"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showDropdown = false"
        class="fixed inset-0 bg-black bg-opacity-50"
        style="display: none; z-index: 99998; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;"
    ></div>

    <!-- Notification Sidebar Panel -->
    <div
        x-show="showDropdown"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform translate-x-full opacity-0"
        x-transition:enter-end="transform translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="transform translate-x-0 opacity-100"
        x-transition:leave-end="transform translate-x-full opacity-0"
        class="fixed top-0 right-0 h-screen w-96 bg-white shadow-2xl border-l border-gray-200 overflow-hidden"
        style="display: none; z-index: 99999; position: fixed !important; top: 0 !important; right: 0 !important; height: 100vh !important; max-width: calc(100vw - 2rem);"
    >
        <!-- Panel Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-linear-to-r from-emerald-50 to-teal-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                </div>
                <button
                    @click="showDropdown = false"
                    class="p-1 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex items-center justify-between mt-2">
                @if($unreadCount > 0)
                    <p class="text-sm text-gray-600">{{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}</p>
                    <button
                        wire:click="markAllAsRead"
                        class="text-sm text-emerald-600 hover:text-emerald-800 font-medium px-3 py-1 rounded-full hover:bg-emerald-50 transition-colors"
                    >
                        Mark all read
                    </button>
                @else
                    <p class="text-sm text-gray-500">You're all caught up!</p>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="flex-1 overflow-y-auto" style="height: calc(100vh - 140px);">
            @if(empty($notifications))
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-800 mb-2">No new notifications</h3>
                    <p class="text-sm text-gray-500">You're all caught up! We'll notify you when something new happens.</p>
                </div>
            @else
                @foreach($notifications as $notification)
                    <div
                        class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150 cursor-pointer {{ !isset($notification['read_at']) ? 'bg-blue-50 border-l-4 border-l-emerald-500' : '' }}"
                        wire:click="markAsRead('{{ $notification['id'] }}')"
                    >
                        <!-- SavedSearch Match Notification -->
                        @if($notification['type'] === 'App\Notifications\SavedSearchMatch')
                            <div class="flex items-start space-x-3">
                                <!-- Match Icon -->
                                <div class="shrink-0 mt-1">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Notification Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-800">
                                            {{ $notification['data']['title'] }}
                                        </p>
                                        @if(isset($notification['data']['match_score']))
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $notification['data']['match_score'] }}% match
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $notification['data']['message'] }}
                                    </p>

                                    <!-- Property Info -->
                                    @if(isset($notification['data']['properties'][0]))
                                        @php $property = $notification['data']['properties'][0]; @endphp
                                        <div class="mt-2 p-2 bg-gray-50 rounded-md">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-800">{{ $property['title'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $property['area'] }}, {{ $property['city'] }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-semibold text-green-600">₦{{ number_format($property['price']) }}</p>
                                                    <p class="text-xs text-gray-500 capitalize">{{ $property['listing_type'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <!-- Generic Notification -->
                            <div class="flex items-start space-x-3">
                                <div class="shrink-0 mt-1">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $notification['data']['title'] ?? 'Notification' }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $notification['data']['message'] ?? $notification['data']['body'] ?? 'You have a new notification' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Panel Footer -->
        @if(!empty($notifications))
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <a href="#" class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 block text-center">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>