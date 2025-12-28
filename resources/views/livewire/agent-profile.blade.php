<div class="min-h-screen bg-gray-50 font-sans text-gray-900">
    <!-- Clean Header Section -->
    <div class="bg-white border-b border-gray-100 relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-72 h-72 bg-gradient-to-tr from-blue-50 to-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 relative z-10">
            <!-- Breadcrumb -->
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
                <a href="{{ route('agents') }}" wire:navigate class="hover:text-emerald-600 transition-colors">Agents</a>
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-medium text-gray-900">{{ $agent->name }}</span>
            </nav>

            <!-- Agent Profile Header -->
            <div class="flex flex-col lg:flex-row lg:items-start gap-8 lg:gap-12">
                <!-- Agent Avatar -->
                <div class="shrink-0 relative group mx-auto lg:mx-0">
                    <div class="absolute -inset-1 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full opacity-20 group-hover:opacity-40 blur transition duration-500"></div>
                    @if($agentProfile->profile_photo_url)
                        <img src="{{ $agentProfile->profile_photo_url }}"
                             alt="{{ $agent->name }}"
                             class="relative w-32 h-32 lg:w-48 lg:h-48 rounded-full border-4 border-white shadow-xl object-cover z-10">
                    @else
                        <div class="relative w-32 h-32 lg:w-48 lg:h-48 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center border-4 border-white shadow-xl z-10">
                            <span class="text-white font-bold text-5xl lg:text-7xl">{{ substr($agent->name, 0, 1) }}</span>
                        </div>
                    @endif

                    @if($agentProfile->is_verified)
                        <div class="absolute bottom-2 right-2 lg:bottom-4 lg:right-4 z-20">
                            <div class="bg-white text-blue-600 rounded-full p-1.5 shadow-lg border border-gray-100" title="Verified Agent">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Agent Details -->
                <div class="flex-1 text-center lg:text-left">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <div class="flex items-center justify-center lg:justify-start gap-3 mb-2">
                                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">{{ $agent->name }}</h1>
                                @if($agentProfile->is_verified)
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded-full text-xs font-bold border border-blue-100">
                                        Verified
                                    </span>
                                @endif
                            </div>

                            @if($agentProfile->agency)
                                <p class="text-lg text-gray-600 font-medium mb-4 flex items-center justify-center lg:justify-start gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ $agentProfile->agency->name }}
                                </p>
                            @else
                                <p class="text-lg text-emerald-600 font-medium mb-4">Independent Agent</p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-end">
                            @if($agentProfile->user->phone)
                                <a href="tel:{{ $agentProfile->phone }}"
                                   class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:border-emerald-500 hover:text-emerald-600 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>Call</span>
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agentProfile->phone) }}?text=Hi, I'm interested in your property listings on HomeBaze"
                                   target="_blank"
                                   class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-2.462-.96-4.779-2.705-6.526-1.746-1.746-4.065-2.707-6.526-2.709-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.092-.638zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 mt-8">
                        <!-- Rating -->
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ number_format($reviewStats['average_rating'], 1) }}</div>
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">Average Rating</div>
                                </div>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8zM8 14v.01M12 14v.01M16 14v.01"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $agentProfile->years_experience ?? 0 }}<span class="text-sm font-normal text-gray-500 ml-1">years</span></div>
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">Experience</div>
                                </div>
                            </div>
                        </div>

                        <!-- Listings -->
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $agentProfile->properties_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">Active Listings</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bio & Specializations -->
                    <div class="space-y-6">
                        @if($agentProfile->bio)
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">About</h3>
                                <p class="text-gray-600 leading-relaxed">{{ $agentProfile->bio }}</p>
                            </div>
                        @endif

                        @if($agentProfile->specializations)
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Specializations</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(explode(',', $agentProfile->specializations) as $specialization)
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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Reviews Section (Left Column) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Review Statistics -->
                @if($reviewStats['total_reviews'] > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            Review Statistics
                            <span class="text-sm font-normal text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $reviewStats['total_reviews'] }}</span>
                        </h2>

                        <!-- Rating Distribution -->
                        <div class="space-y-4">
                            @for($i = 5; $i >= 1; $i--)
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-1 w-12 shrink-0">
                                        <span class="text-sm font-bold text-gray-700">{{ $i }}</span>
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full transition-all duration-500"
                                             style="width: {{ $reviewStats['rating_distribution'][$i]['percentage'] }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500 w-12 text-right font-medium">{{ $reviewStats['rating_distribution'][$i]['count'] }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif

                <!-- Reviews Filters & Sorting -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Filters -->
                        <div class="flex items-center space-x-3">
                            <label class="text-sm font-semibold text-gray-700">Filter:</label>
                            <select wire:model.live="reviewFilter" class="bg-gray-50 border-none text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 py-2 pl-3 pr-8 cursor-pointer hover:bg-gray-100 transition-colors">
                                <option value="all">All Reviews</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>

                        <!-- Sorting -->
                        <div class="flex items-center space-x-3">
                            <label class="text-sm font-semibold text-gray-700">Sort by:</label>
                            <select wire:model.live="sortBy" class="bg-gray-50 border-none text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 py-2 pl-3 pr-8 cursor-pointer hover:bg-gray-100 transition-colors">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="highest">Highest Rated</option>
                                <option value="lowest">Lowest Rated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="space-y-6">
                    @forelse($reviews as $review)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 transition-all hover:shadow-md">
                            <!-- Review Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                        {{ substr($review->reviewer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $review->reviewer->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $review->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center bg-yellow-50 px-2 py-1 rounded-lg">
                                    <span class="font-bold text-yellow-700 mr-1.5">{{ $review->rating }}.0</span>
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Review Title -->
                            @if($review->title)
                                <h5 class="text-lg font-bold text-gray-900 mb-3">{{ $review->title }}</h5>
                            @endif

                            <!-- Review Content -->
                            <p class="text-gray-600 leading-relaxed mb-6">{{ $review->comment }}</p>

                            <!-- Helpful Count -->
                            @if($review->helpful_count > 0)
                                <div class="flex items-center space-x-2 text-sm text-gray-500 bg-gray-50 inline-flex px-3 py-1.5 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V9a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905V9L7 20M7 20L4.5 18.5M7 20l3.5-1.5M14 10h-5V9a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2h6v-2"></path>
                                    </svg>
                                    <span>{{ $review->helpful_count }} {{ Str::plural('person', $review->helpful_count) }} found this helpful</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 border-dashed">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No reviews yet</h3>
                            <p class="text-gray-500">Be the first to share your experience with {{ $agent->name }}</p>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($reviews->hasPages())
                        <div class="mt-8">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Write Review Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Review This Agent</h3>
                    @livewire('agent-rating-form', ['agent' => $agent])
                </div>

                <!-- Recent Properties -->
                @if($recentProperties->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Recent Listings</h3>
                            <a href="{{ route('properties.search', ['agent' => $agentProfile->id]) }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">View All</a>
                        </div>
                        
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

                        <div class="mt-6 pt-6 border-t border-gray-50">
                            <a href="{{ route('properties.search', ['agent' => $agentProfile->id]) }}"
                               class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-3 px-4 rounded-xl transition-colors">
                                Browse All Listings
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
