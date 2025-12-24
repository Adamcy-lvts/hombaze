<div class="min-h-screen bg-gray-50 font-sans text-gray-900">
    <div class="bg-white border-b border-gray-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-72 h-72 bg-gradient-to-tr from-blue-50 to-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 relative z-10">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
                <a href="{{ route('landing') }}" wire:navigate class="hover:text-emerald-600 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Home
                </a>
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('agencies') }}" wire:navigate class="hover:text-emerald-600 transition-colors">Agencies</a>
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-medium text-gray-900">{{ $agency->name }}</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-start gap-8 lg:gap-12">
                <div class="shrink-0 relative group mx-auto lg:mx-0">
                    <div class="absolute -inset-1 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-3xl opacity-20 group-hover:opacity-40 blur transition duration-500"></div>
                    @if($agency->logo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($agency->logo) }}"
                             alt="{{ $agency->name }}"
                             class="relative w-36 h-36 lg:w-52 lg:h-52 rounded-3xl border-4 border-white shadow-xl object-contain bg-white z-10 p-6">
                    @else
                        <div class="relative w-36 h-36 lg:w-52 lg:h-52 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl flex items-center justify-center border-4 border-white shadow-xl z-10">
                            <span class="text-white font-bold text-5xl lg:text-6xl">{{ substr($agency->name, 0, 1) }}</span>
                        </div>
                    @endif

                    @if($agency->is_verified)
                        <div class="absolute bottom-3 right-3 lg:bottom-4 lg:right-4 z-20">
                            <div class="bg-white text-blue-600 rounded-full p-1.5 shadow-lg border border-gray-100" title="Verified Agency">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex-1 text-center lg:text-left">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <div class="flex items-center justify-center lg:justify-start gap-3 mb-2">
                                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">{{ $agency->name }}</h1>
                                @if($agency->is_verified)
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded-full text-xs font-bold border border-blue-100">
                                        Verified
                                    </span>
                                @endif
                            </div>

                            <p class="text-lg text-gray-600 font-medium mb-4 flex items-center justify-center lg:justify-start gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $agency->address_string ?: 'Location not listed' }}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-end">
                            @if($agency->phone)
                                <a href="tel:{{ $agency->phone }}"
                                   class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:border-emerald-500 hover:text-emerald-600 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>Call</span>
                                </a>
                            @endif
                            @if($agency->email)
                                <a href="mailto:{{ $agency->email }}"
                                   class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:border-emerald-500 hover:text-emerald-600 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Email</span>
                                </a>
                            @endif
                            @if($agency->website)
                                @php
                                    $websiteUrl = \Illuminate\Support\Str::startsWith($agency->website, ['http://', 'https://'])
                                        ? $agency->website
                                        : 'https://' . $agency->website;
                                @endphp
                                <a href="{{ $websiteUrl }}" target="_blank"
                                   class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    <span>Website</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 mt-8">
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ number_format($agency->rating, 1) }}</div>
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $agency->total_reviews }} Reviews</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8zM8 14v.01M12 14v.01M16 14v.01"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $agency->years_in_business ?? 0 }}<span class="text-sm font-normal text-gray-500 ml-1">years</span></div>
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">In Business</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $activeListings }}</div>
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">Active Listings</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                            <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-2">Active Agents</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $activeAgents }}</div>
                        </div>
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                            <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-2">License</div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $agency->license_number ?: 'Not listed' }}
                            </div>
                            @if($agency->license_expiry_date)
                                <div class="text-xs text-gray-500 mt-1">Expires {{ $agency->license_expiry_date->format('M j, Y') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-6 mt-8">
                        @if($agency->description)
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">About</h3>
                                <p class="text-gray-600 leading-relaxed">{{ $agency->description }}</p>
                            </div>
                        @endif

                        @if($agency->specializations)
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Specializations</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(explode(',', $agency->specializations) as $specialization)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ trim($specialization) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Top Agents</h2>
                        <a href="{{ route('agents') }}" wire:navigate class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Browse all agents</a>
                    </div>

                    @if($topAgents->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($topAgents as $agent)
                                <a href="{{ route('agent.profile', $agent->user) }}" wire:navigate class="group flex items-center gap-4 p-4 border border-gray-100 rounded-xl hover:border-emerald-200 hover:shadow-md transition-all">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                                        @if($agent->user?->avatar)
                                            <img src="{{ $agent->user->avatar }}" alt="{{ $agent->user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-gray-400 font-bold text-lg">{{ substr($agent->user?->name ?? 'A', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold text-gray-900 group-hover:text-emerald-600 transition-colors truncate">
                                            {{ $agent->user?->name ?? 'Agent' }}
                                        </div>
                                        <div class="text-xs text-gray-500">Rating {{ number_format($agent->rating ?? 0, 1) }} · {{ $agent->properties_count ?? 0 }} listings</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-gray-500">No agents listed yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Recent Listings</h3>
                        <a href="{{ route('properties.search', ['agency' => $agency->id]) }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">View All</a>
                    </div>

                    @if($recentProperties->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentProperties as $property)
                                <a href="{{ route('property.show', $property) }}" wire:navigate class="block group bg-white border border-gray-100 rounded-xl p-2 hover:border-emerald-200 hover:shadow-md transition-all duration-200">
                                    <div class="flex space-x-3">
                                        <div class="relative w-20 h-20 shrink-0">
                                            @if($property->getFirstMediaUrl('featured'))
                                                <img src="{{ $property->getFirstMediaUrl('featured', 'thumb') }}"
                                                     alt="{{ $property->title }}"
                                                     class="w-full h-full rounded-lg object-cover">
                                            @else
                                                <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-black/5 rounded-lg group-hover:bg-transparent transition-colors"></div>
                                        </div>

                                        <div class="flex-1 min-w-0 py-1">
                                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-emerald-600 transition-colors truncate mb-1">
                                                {{ $property->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mb-2 truncate">
                                                {{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-sm font-bold text-emerald-600">
                                                ₦{{ number_format($property->price) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-gray-500">No listings published yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
