<div class="min-h-screen bg-gray-50 font-sans text-gray-900">
    <!-- Header Section -->
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-100/50 sticky top-0 z-30 backdrop-blur-xl bg-white/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <!-- Welcome Section -->
                <div>
                     <!-- Breadcrumb (Clean) -->
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 font-medium">
                        <a href="{{ route('landing') }}" class="hover:text-emerald-600 transition-colors">Home</a>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-900">Dashboard</span>
                    </div>

                    <div class="flex items-center gap-5">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100/50">
                                @if(auth()->user()->profile_photo_url)
                                    <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full object-cover rounded-2xl" alt="{{ auth()->user()->name }}">
                                @else
                                    <span class="text-xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
                            <p class="text-gray-500 mt-1">Here's what's happening with your property search today.</p>
                        </div>
                    </div>
                </div>

                @php
                    $activeSubscription = auth()->user()?->activeSmartSearchSubscription();
                @endphp
                <div class="flex items-center">
                    @if($activeSubscription)
                        <div class="bg-white rounded-2xl p-1.5 shadow-sm border border-gray-100 flex items-center gap-3 pr-4">
                            <div class="bg-emerald-50 rounded-xl p-2.5 text-emerald-600">
                                <x-heroicon-o-sparkles class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400">Current Plan</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-gray-900">{{ $activeSubscription->getTierName() }}</span>
                                    <span class="text-xs text-gray-400 font-medium">
                                        ({{ $activeSubscription->hasUnlimitedSearches() ? '∞' : $activeSubscription->getRemainingSearches() }} searches left)
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('customer.searches.index') }}" class="ml-2 p-2 rounded-lg hover:bg-gray-50 text-gray-400 hover:text-emerald-600 transition-colors">
                                <x-heroicon-o-chevron-right class="w-5 h-5" />
                            </a>
                        </div>
                    @else
                        <a href="{{ route('smartsearch.pricing') }}" class="group flex items-center gap-4 bg-gray-900 hover:bg-gray-800 text-white pl-4 pr-1.5 py-1.5 rounded-2xl transition-all shadow-lg hover:shadow-xl hover:scale-[1.02]">
                            <div>
                                <p class="text-xs font-semibold text-gray-300">Upgrade to SmartSearch</p>
                                <p class="font-bold text-white">Unlock AI Matching</p>
                            </div>
                            <div class="bg-white/10 rounded-xl p-2.5 group-hover:bg-white/20 transition-colors">
                                <x-heroicon-o-arrow-right class="w-5 h-5" />
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Saved Properties -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <x-heroicon-o-heart class="w-5 h-5" />
                    </div>
                    @if(isset($stats['saved_properties']['change']))
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">{{ $stats['saved_properties']['change'] }}</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-500">Saved Properties</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['saved_properties']['total'] }}</p>
            </div>

            <!-- Active Inquiries -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5" />
                    </div>
                    @if(isset($stats['active_inquiries']['change']))
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">{{ $stats['active_inquiries']['change'] }}</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-500">Active Inquiries</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['active_inquiries']['total'] }}</p>
            </div>

            <!-- Property Views -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </div>
                    @if(isset($stats['property_views']['change']))
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">{{ $stats['property_views']['change'] }}</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-500">Property Views</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['property_views']['total'] }}</p>
            </div>

            <!-- Scheduled Viewings -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <x-heroicon-o-calendar class="w-5 h-5" />
                    </div>
                    @if(isset($stats['scheduled_viewings']['change']))
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">{{ $stats['scheduled_viewings']['change'] }}</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-500">Scheduled Viewings</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['scheduled_viewings']['total'] }}</p>
            </div>
        </div>

        <!-- Recommended Properties -->
        @if($recommendedProperties->count() > 0)
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recommended for You</h2>
                    <a href="{{ route('properties.search') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">View all</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recommendedProperties as $property)
                        <a href="{{ route('property.show', $property) }}" class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                                @if($property->getFirstMediaUrl('featured'))
                                    <img src="{{ $property->getFirstMediaUrl('featured') }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <x-heroicon-o-home class="w-12 h-12" />
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-gray-900 shadow-sm">
                                    {{ ucfirst($property->listing_type) }}
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-bold text-gray-900 line-clamp-1 group-hover:text-emerald-600 transition-colors">{{ $property->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                                    <p class="text-lg font-bold text-emerald-600">₦{{ number_format($property->price) }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 font-medium">
                                        @if($property->bedrooms) <span>{{ $property->bedrooms }} Beds</span> @endif
                                        @if($property->toilets) <span>{{ $property->toilets }} Baths</span> @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Recent Activity</h2>
                    <a href="{{ route('customer.activity') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">View all</a>
                </div>

                @if($recentActivity->count() > 0)
                    <div class="space-y-6">
                        @foreach($recentActivity->take(5) as $activity)
                            <div class="flex gap-4">
                                <div class="shrink-0 mt-1">
                                    @if($activity['icon'] === 'heart')
                                        <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                                            <x-heroicon-o-heart class="w-4 h-4" />
                                        </div>
                                    @elseif($activity['icon'] === 'chat-bubble-left-ellipsis')
                                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4" />
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-600">
                                            <x-heroicon-o-clock class="w-4 h-4" />
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $activity['time']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <x-heroicon-o-clock class="w-6 h-6" />
                        </div>
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('properties.search') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Search Properties</p>
                                <p class="text-xs text-gray-500">Find your dream home</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('customer.saved-properties') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 group-hover:bg-purple-100 transition-colors">
                                <x-heroicon-o-heart class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Saved Properties</p>
                                <p class="text-xs text-gray-500">{{ $stats['saved_properties']['total'] }} saved items</p>
                            </div>
                        </a>

                        <a href="{{ route('customer.searches.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-100 transition-colors">
                                <x-heroicon-o-bell class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">SmartSearch</p>
                                <p class="text-xs text-gray-500">Manage alerts</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-gradient-to-br from-emerald-900 to-emerald-800 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-start gap-4">
                        <div class="p-2 bg-white/10 rounded-lg shrink-0">
                            <x-heroicon-o-light-bulb class="w-6 h-6 text-yellow-300" />
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">Pro Tip</h3>
                            <p class="text-sm text-emerald-100 leading-relaxed">
                                Complete your profile preferences to get AI-powered recommendations tailored just for you.
                            </p>
                            <a href="{{ route('customer.settings') }}" class="inline-block mt-4 text-sm font-semibold text-white border-b border-emerald-400 pb-0.5 hover:text-emerald-200 transition-colors">
                                Update Preferences &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
