<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Welcome back, {{ auth()->user()->name }}!
                </h2>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Profile {{ $profileCompletion['percentage'] }}% complete</span>
                <div class="w-20 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $profileCompletion['percentage'] }}%"></div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Profile Completion Alert -->
            @if($profileCompletion['percentage'] < 80)
                <div class="bg-linear-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-start space-x-3">
                            <svg class="w-8 h-8 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-blue-900 mb-2">
                                    Complete your profile for better recommendations
                                </h3>
                                <p class="text-blue-700 text-sm">
                                    Missing: {{ implode(', ', array_slice($profileCompletion['missing'], 0, 2)) }}
                                    @if(count($profileCompletion['missing']) > 2)
                                        and {{ count($profileCompletion['missing']) - 2 }} more
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="#"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                Complete Profile
                            </a>
                            <button class="text-blue-600 hover:text-blue-800 px-4 py-2 font-medium">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Saved Properties</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['saved_properties'] }}</p>
                            <p class="text-xs text-green-600 mt-1">+{{ rand(1, 4) }} this month</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Inquiries</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_inquiries'] }}</p>
                            <p class="text-xs text-orange-600 mt-1">{{ max(0, $stats['active_inquiries'] - 1) }} pending</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Property Views</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['property_views'] }}</p>
                            <p class="text-xs text-blue-600 mt-1">+{{ rand(3, 8) }} this week</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Scheduled Viewings</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['scheduled_viewings'] }}</p>
                            <p class="text-xs text-green-600 mt-1">{{ max(0, $stats['scheduled_viewings'] - 1) }} this week</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                <div class="flex items-center space-x-2 mb-4">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('properties.search') }}"
                       class="flex items-center space-x-3 p-4 bg-linear-to-r from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-colors group">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900">Search Properties</p>
                            <p class="text-xs text-blue-700">Find your dream home</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.saved-properties') }}"
                       class="flex items-center space-x-3 p-4 bg-linear-to-r from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-colors group">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-purple-900">Saved Properties</p>
                            <p class="text-xs text-purple-700">{{ $stats['saved_properties'] }} saved</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.inquiries') }}"
                       class="flex items-center space-x-3 p-4 bg-linear-to-r from-orange-50 to-orange-100 rounded-lg hover:from-orange-100 hover:to-orange-200 transition-colors group">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-orange-900">My Inquiries</p>
                            <p class="text-xs text-orange-700">{{ $stats['active_inquiries'] }} active</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.settings') }}"
                       class="flex items-center space-x-3 p-4 bg-linear-to-r from-gray-50 to-gray-100 rounded-lg hover:from-gray-100 hover:to-gray-200 transition-colors group">
                        <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Settings</p>
                            <p class="text-xs text-gray-700">Manage preferences</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    @if($activity['type'] === 'view')
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    @elseif($activity['type'] === 'save')
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    @elseif($activity['type'] === 'inquiry')
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent activity</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recommended Properties -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Recommended for You</h3>
                        </div>
                        <a href="{{ route('properties.search') }}" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recommendedProperties as $property)
                            <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                    @if($property->getFirstMediaUrl('gallery'))
                                        <img src="{{ $property->getFirstMediaUrl('gallery') }}"
                                             alt="{{ $property->title }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            🏠
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $property->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $property->area->name ?? 'Unknown Area' }}</p>
                                    <p class="text-sm font-semibold text-green-600">₦{{ number_format($property->price) }}</p>
                                </div>
                                <a href="{{ route('property.show', $property->slug) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">Complete your profile for personalized recommendations</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Saved Properties & Inquiries Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Saved Properties Summary -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">💾 Your Saved Properties ({{ $savedProperties['total'] }})</h3>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Manage All</a>
                    </div>
                    @if($savedProperties['total'] > 0)
                        <div class="space-y-2">
                            @foreach($savedProperties['locations'] as $location)
                                <div class="flex items-center justify-between py-2">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $location['location'] }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $location['count'] }} properties</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex space-x-3">
                                <a href="#"
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors">
                                    Create Alert
                                </a>
                                <a href="#"
                                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-2 px-4 rounded-lg font-medium transition-colors">
                                    Export List
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">You haven't saved any properties yet</p>
                            <a href="{{ route('properties.search') }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Start Browsing
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Active Inquiries & Viewings -->
                <div class="space-y-6">
                    <!-- Active Inquiries -->
                    <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Active Inquiries ({{ $activeInquiries->count() }})</h3>
                            </div>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                        </div>
                        @forelse($activeInquiries as $inquiry)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $inquiry->property->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        Agent: {{ $inquiry->responder->name ?? 'Not assigned' }} -
                                        @if($inquiry->isResponded())
                                            <span class="text-green-600">Responded {{ $inquiry->responded_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-orange-600">Pending Response</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="w-3 h-3 rounded-full {{ $inquiry->isResponded() ? 'bg-green-500' : 'bg-orange-500' }}"></div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No active inquiries</p>
                        @endforelse
                    </div>

                    <!-- Upcoming Viewings -->
                    <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Upcoming Viewings ({{ $upcomingViewings->count() }})</h3>
                            </div>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Schedule More</a>
                        </div>
                        @forelse($upcomingViewings as $viewing)
                            <div class="flex items-center space-x-3 py-3 border-b border-gray-100 last:border-b-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $viewing->property->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $viewing->scheduled_date->format('M j, Y') }} at {{ $viewing->scheduled_time->format('g:i A') }}
                                    </p>
                                    <p class="text-xs text-blue-600">Agent: {{ $viewing->agent->name }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No upcoming viewings</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>