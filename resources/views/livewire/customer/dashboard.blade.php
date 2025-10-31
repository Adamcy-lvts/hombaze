<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 relative overflow-hidden">
    <!-- Subtle Background Elements -->
    <div class="absolute inset-0 opacity-30">
        <div class="floating-element absolute top-1/4 right-1/4 w-32 h-32 bg-gradient-to-br from-emerald-400/8 to-teal-500/6 rounded-full blur-3xl"></div>
        <div class="floating-element absolute bottom-1/3 left-1/4 w-40 h-40 bg-gradient-to-br from-blue-400/6 to-indigo-500/4 rounded-full blur-3xl"></div>
    </div>

    <!-- Header Section -->
    <div class="relative z-30 bg-white/95 backdrop-blur-xl border-b border-white/50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <!-- Welcome Section -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg flex-shrink-0">
                        <x-heroicon-o-user class="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">Welcome back, {{ auth()->user()->name }}!</h1>
                        <p class="text-sm sm:text-base text-gray-600 mt-1 leading-tight">Manage your property search and preferences</p>
                    </div>
                </div>

                <!-- Profile Completion (Desktop) -->
                <div class="hidden lg:flex items-center space-x-4 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Profile {{ $profileCompletion['percentage'] }}% complete</p>
                        <div class="w-32 bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ $profileCompletion['percentage'] }}%"></div>
                        </div>
                    </div>

                    @if($profileCompletion['percentage'] < 100)
                        <a href="{{ route('customer.settings') }}"
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl transition-all duration-500 transform hover:scale-105 shadow-lg text-sm whitespace-nowrap">
                            Complete Profile
                        </a>
                    @else
                        <button class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm whitespace-nowrap">
                            <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                            Profile Complete
                        </button>
                    @endif
                </div>

                <!-- Profile Completion Mini (Tablet) -->
                <div class="hidden md:flex lg:hidden items-center justify-end">
                    @if($profileCompletion['percentage'] < 100)
                        <a href="{{ route('customer.settings') }}"
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg transition-all duration-500 transform hover:scale-105 shadow-lg text-sm">
                            <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-2" />
                            Complete Profile
                        </a>
                    @else
                        <div class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg text-sm">
                            <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                            Complete
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Profile Completion Banner (Mobile) -->
        @if($profileCompletion['percentage'] < 100)
            <div class="md:hidden bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <div class="flex items-start sm:items-center space-x-3 sm:space-x-4">
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg flex-shrink-0">
                        <x-heroicon-o-chart-bar class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 leading-tight">Complete your profile for better recommendations</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-tight">Missing: Set budget range, Select preferred property types and 1 more</p>

                        <!-- Progress Bar -->
                        <div class="mt-3 sm:mt-4">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-gray-700">{{ $profileCompletion['percentage'] }}% complete</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full transition-all duration-300"
                                     style="width: {{ $profileCompletion['percentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:gap-3">
                            <a href="{{ route('customer.settings') }}"
                               class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2.5 sm:py-2 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl transition-all duration-500 transform hover:scale-105 shadow-lg text-sm">
                                <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-2" />
                                Complete Profile
                            </a>
                            <button class="inline-flex items-center justify-center px-4 py-2.5 sm:py-2 text-gray-500 text-sm hover:text-gray-700 transition-colors">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Saved Properties -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-500 hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Saved Properties</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $stats['saved_properties']['total'] }}</p>
                        <p class="text-xs sm:text-sm text-green-600 mt-1">{{ $stats['saved_properties']['change'] }}</p>
                    </div>
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg">
                        <x-heroicon-o-heart class="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                    </div>
                </div>
            </div>

            <!-- Active Inquiries -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-500 hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Active Inquiries</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $stats['active_inquiries']['total'] }}</p>
                        <p class="text-xs sm:text-sm text-orange-600 mt-1">{{ $stats['active_inquiries']['change'] }}</p>
                    </div>
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-orange-500 to-red-500 shadow-lg">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                    </div>
                </div>
            </div>

            <!-- Property Views -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-500 hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Property Views</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $stats['property_views']['total'] }}</p>
                        <p class="text-xs sm:text-sm text-purple-600 mt-1">{{ $stats['property_views']['change'] }}</p>
                    </div>
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg">
                        <x-heroicon-o-eye class="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                    </div>
                </div>
            </div>

            <!-- Scheduled Viewings -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-500 hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Scheduled Viewings</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $stats['scheduled_viewings']['total'] }}</p>
                        <p class="text-xs sm:text-sm text-green-600 mt-1">{{ $stats['scheduled_viewings']['change'] }}</p>
                    </div>
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg">
                        <x-heroicon-o-calendar class="w-6 h-6 sm:w-8 sm:h-8 text-white" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Properties - Full Width Priority -->
        @if($recommendedProperties->count() > 0)
        <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg mr-3">
                            <x-heroicon-o-sparkles class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Recommended for You</h2>
                    </div>
                    <a href="{{ route('properties.search') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 font-medium">View all</a>
                </div>

                @if($recommendedProperties->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        @foreach($recommendedProperties as $property)
                            <div class="group">
                                <a href="{{ route('property.show', $property) }}"
                                   class="block bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg hover:border-emerald-300 transition-all duration-300 overflow-hidden relative">

                                 

                                    <div class="flex items-center space-x-4 p-4">
                                        @if($property->getFirstMediaUrl('featured'))
                                            <img src="{{ $property->getFirstMediaUrl('featured') }}"
                                                 alt="{{ $property->title }}"
                                                 class="w-20 h-20 object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <x-heroicon-o-home class="w-10 h-10 text-gray-400" />
                                            </div>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors leading-tight">
                                                {{ $property->title }}
                                            </h3>
                                            <p class="text-sm text-gray-600 flex items-center mt-1">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $property->area->name ?? '' }}, {{ $property->area->city->name ?? '' }}
                                            </p>
                                            <p class="text-base font-bold text-emerald-600 mt-2">
                                                ₦{{ number_format($property->price) }}
                                                <span class="text-xs font-normal text-gray-500 ml-1">
                                                    / {{ ucfirst($property->listing_type ?? 'sale') }}
                                                </span>
                                            </p>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <div class="p-2 text-gray-400 group-hover:text-emerald-600 transition-colors">
                                                <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5" />
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- View More Button -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('properties.search') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            View All Properties
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 mx-auto mb-4 p-3 bg-gray-100 rounded-full">
                            <x-heroicon-o-sparkles class="w-6 h-6 text-gray-400" />
                        </div>
                        <p class="text-gray-600">No recommendations yet</p>
                        <p class="text-sm text-gray-500 mt-1">Complete your profile to get personalized recommendations</p>
                        <div class="flex flex-col sm:flex-row gap-2 mt-3">
                            <a href="{{ route('customer.preferences') }}"
                               class="inline-flex items-center justify-center px-4 py-2 text-sm bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl transition-all duration-500 transform hover:scale-105 shadow-lg">
                                <x-heroicon-o-sparkles class="w-4 h-4 mr-2" />
                                Set Preferences
                            </a>
                            <a href="{{ route('customer.settings') }}"
                               class="inline-flex items-center justify-center px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl transition-all duration-300">
                                <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-2" />
                                Account Settings
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Activity Section (Compact) - Only shows when recommendations exist -->
        @if($recommendedProperties->count() > 0 && $recentActivity->count() > 0)
        <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mr-3">
                        <x-heroicon-o-clock class="w-5 h-5 text-white" />
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                </div>
                <a href="{{ route('customer.activity') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all</a>
            </div>

            <div class="space-y-3">
                @foreach($recentActivity->take(3) as $activity)
                    <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50/50 hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            @if($activity['icon'] === 'heart')
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <x-heroicon-o-heart class="w-4 h-4 text-purple-600" />
                                </div>
                            @elseif($activity['icon'] === 'chat-bubble-left-ellipsis')
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4 text-orange-600" />
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <x-heroicon-o-eye class="w-4 h-4 text-blue-600" />
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ $activity['description'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <p class="text-xs text-gray-500">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Full-width Recent Activity Section (When no recommendations) -->
        @if($recommendedProperties->count() === 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Recent Activity -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mr-3">
                            <x-heroicon-o-clock class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Recent Activity</h2>
                    </div>
                    <a href="{{ route('customer.activity') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 font-medium">View all</a>
                </div>

                @if($recentActivity->count() > 0)
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-start space-x-3 sm:space-x-4 p-3 sm:p-4 rounded-lg sm:rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-all duration-300">
                                <div class="flex-shrink-0">
                                    @if($activity['icon'] === 'heart')
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                            <x-heroicon-o-heart class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" />
                                        </div>
                                    @elseif($activity['icon'] === 'chat-bubble-left-ellipsis')
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" />
                                        </div>
                                    @else
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                            <x-heroicon-o-eye class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-medium text-gray-900">{{ $activity['title'] }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $activity['description'] }}</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-2">{{ $activity['time']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 sm:py-8">
                        <div class="w-12 h-12 mx-auto mb-4 p-3 bg-gray-100 rounded-full">
                            <x-heroicon-o-clock class="w-6 h-6 text-gray-400" />
                        </div>
                        <p class="text-gray-600">No recent activity</p>
                        <p class="text-sm text-gray-500 mt-1">Start exploring properties to see your activity here</p>
                    </div>
                @endif
            </div>

            <!-- Tips & Insights -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg mr-3">
                        <x-heroicon-o-light-bulb class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Tips & Insights</h2>
                </div>

                <div class="space-y-4">
                    <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200">
                        <h3 class="text-sm font-semibold text-emerald-800">Complete Your Profile</h3>
                        <p class="text-sm text-emerald-700 mt-1">Add your preferences to get better property recommendations tailored to your needs.</p>
                    </div>
                    <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                        <h3 class="text-sm font-semibold text-blue-800">Set Up Saved Searches</h3>
                        <p class="text-sm text-blue-700 mt-1">Create saved searches to get notified when new properties matching your criteria are listed.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions Section -->
        <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
            <div class="flex items-center mb-4 sm:mb-6">
                <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-yellow-500 to-orange-500 shadow-lg mr-3">
                    <x-heroicon-o-bolt class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Quick Actions</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search Properties -->
                <a href="{{ route('properties.search') }}"
                   class="group p-3 sm:p-4 rounded-lg sm:rounded-xl bg-white/95 backdrop-blur-xl hover:bg-white border border-gray-300/60 hover:border-emerald-300/60 transition-all duration-500 hover:scale-105 shadow-lg hover:shadow-emerald-500/40 block">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-medium text-gray-900">Search Properties</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Find your dream home</p>
                        </div>
                    </div>
                </a>

                <!-- Saved Properties -->
                <a href="{{ route('customer.saved-properties') }}"
                   class="group p-3 sm:p-4 rounded-lg sm:rounded-xl bg-white/95 backdrop-blur-xl hover:bg-white border border-gray-300/60 hover:border-purple-300/60 transition-all duration-500 hover:scale-105 shadow-lg hover:shadow-purple-500/40 block">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <x-heroicon-o-heart class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-medium text-gray-900">Saved Properties</h3>
                            <p class="text-xs sm:text-sm text-gray-600">{{ $stats['saved_properties']['total'] }} saved</p>
                        </div>
                    </div>
                </a>

                <!-- My Inquiries -->
                <a href="{{ route('customer.inquiries') }}"
                   class="group p-3 sm:p-4 rounded-lg sm:rounded-xl bg-white/95 backdrop-blur-xl hover:bg-white border border-gray-300/60 hover:border-orange-300/60 transition-all duration-500 hover:scale-105 shadow-lg hover:shadow-orange-500/40 block">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-orange-500 to-red-500 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-medium text-gray-900">My Inquiries</h3>
                            <p class="text-xs sm:text-sm text-gray-600">{{ $stats['active_inquiries']['total'] }} active</p>
                        </div>
                    </div>
                </a>

                <!-- My Searches -->
                <a href="{{ route('customer.searches.index') }}"
                   class="group p-3 sm:p-4 rounded-lg sm:rounded-xl bg-white/95 backdrop-blur-xl hover:bg-white border border-gray-300/60 hover:border-blue-300/60 transition-all duration-500 hover:scale-105 shadow-lg hover:shadow-blue-500/40 block">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <x-heroicon-o-magnifying-glass-circle class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-medium text-gray-900">My Searches</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Manage saved searches</p>
                        </div>
                    </div>
                </a>

                <!-- Settings -->
                <a href="{{ route('customer.settings') }}"
                   class="group p-3 sm:p-4 rounded-lg sm:rounded-xl bg-white/95 backdrop-blur-xl hover:bg-white border border-gray-300/60 hover:border-gray-400/60 transition-all duration-500 hover:scale-105 shadow-lg hover:shadow-gray-500/40 block">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-gray-600 to-slate-700 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <x-heroicon-o-cog-6-tooth class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-medium text-gray-900">Settings</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Account settings</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>