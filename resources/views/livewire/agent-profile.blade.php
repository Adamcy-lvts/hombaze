<div class="min-h-screen bg-gray-50">
    <!-- Clean Header Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Simple Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                <a href="{{ route('landing') }}" wire:navigate class="hover:text-emerald-600 transition-colors">Home</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('agents') }}" wire:navigate class="hover:text-emerald-600 transition-colors">Agents</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-medium text-gray-900">{{ $agent->name }}</span>
            </nav>

            <!-- Agent Profile Header -->
            <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-8 gap-8 mb-8">
                <!-- Agent Avatar & Basic Info -->
                <div class="shrink-0 text-center mb-6 lg:mb-0 lg:text-left">
                    @if($agentProfile->profile_photo_url)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($agentProfile->profile_photo_url) }}"
                             alt="{{ $agent->name }}"
                             class="w-32 h-32 lg:w-40 lg:h-40 rounded-full border-4 border-white shadow-lg object-cover mx-auto lg:mx-0">
                    @else
                        <div class="w-32 h-32 lg:w-40 lg:h-40 bg-linear-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center border-4 border-white shadow-lg mx-auto lg:mx-0">
                            <span class="text-white font-bold text-4xl lg:text-5xl">{{ substr($agent->name, 0, 1) }}</span>
                        </div>
                    @endif

                    @if($agentProfile->is_verified)
                        <div class="inline-flex items-center space-x-2 bg-emerald-100 border border-emerald-200 text-emerald-700 px-3 py-1.5 rounded-lg font-semibold text-sm mt-4">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Verified Agent</span>
                        </div>
                    @endif
                </div>

                <!-- Agent Details -->
                <div class="flex-1 text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-3 mb-3">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">{{ $agent->name }}</h1>
                        @if($agentProfile->is_verified)
                            <div class="inline-flex items-center gap-1 bg-blue-100/90 border border-blue-300/50 text-blue-700 px-3 py-1.5 rounded-lg font-semibold text-xs sm:text-sm shadow-xs">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Verified</span>
                            </div>
                        @endif
                    </div>

                    @if($agentProfile->agency)
                        <p class="text-lg text-blue-600 font-semibold mb-4">{{ $agentProfile->agency->name }}</p>
                    @else
                        <p class="text-lg text-emerald-600 font-semibold mb-4">Independent Agent</p>
                    @endif

                    <!-- Rating & Stats Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <!-- Rating Display -->
                        <div class="flex items-center justify-center sm:justify-start gap-2 bg-white border border-gray-200 rounded-xl px-4 py-3">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $reviewStats['average_rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $reviewStats['average_rating'] }}</span>
                            <span class="text-gray-600">({{ $reviewStats['total_reviews'] }} {{ Str::plural('review', $reviewStats['total_reviews']) }})</span>
                        </div>

                        <!-- Experience -->
                        <div class="flex items-center justify-center sm:justify-start gap-2 bg-white border border-gray-200 rounded-xl px-4 py-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8zM8 14v.01M12 14v.01M16 14v.01"></path>
                            </svg>
                            <span class="font-semibold text-gray-900">{{ $agentProfile->years_experience ?? 0 }} years experience</span>
                        </div>

                        <!-- Properties -->
                        <div class="flex items-center justify-center sm:justify-start gap-2 bg-white border border-gray-200 rounded-xl px-4 py-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="font-semibold text-gray-900">{{ $agentProfile->properties_count ?? 0 }} active listings</span>
                        </div>
                    </div>

                    <!-- Bio -->
                    @if($agentProfile->bio)
                        <p class="text-gray-700 leading-relaxed mb-6">{{ $agentProfile->bio }}</p>
                    @endif

                    <!-- Specializations -->
                    @if($agentProfile->specializations)
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-2">Specializations</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $agentProfile->specializations) as $specialization)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ trim($specialization) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Contact & Review Actions -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap justify-center lg:justify-start gap-3 sm:gap-4">
                        @if($agentProfile->user->phone)
                            <a href="tel:{{ $agentProfile->phone }}" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors w-full sm:w-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>Call Agent</span>
                            </a>
                        @endif

                        @if($agentProfile->user->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agentProfile->phone) }}?text=Hi, I'm interested in your property listings on HomeBaze"
                               target="_blank"
                               class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors w-full sm:w-auto">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-2.462-.96-4.779-2.705-6.526-1.746-1.746-4.065-2.707-6.526-2.709-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.092-.638zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Reviews Section (Left Column) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Review Statistics -->
                @if($reviewStats['total_reviews'] > 0)
                    <div class="bg-white rounded-lg shadow-xs border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Review Statistics</h2>

                        <!-- Rating Distribution -->
                        <div class="space-y-3 mb-8">
                            @for($i = 5; $i >= 1; $i--)
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-1 w-16">
                                        <span class="text-sm font-medium text-gray-700">{{ $i }}</span>
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 h-3 bg-gray-200 rounded-full">
                                        <div class="h-3 bg-yellow-400 rounded-full transition-all duration-300"
                                             style="width: {{ $reviewStats['rating_distribution'][$i]['percentage'] }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right">{{ $reviewStats['rating_distribution'][$i]['count'] }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif

                <!-- Reviews Filters & Sorting -->
                <div class="bg-white rounded-lg shadow-xs border border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Filters -->
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-semibold text-gray-900">Filter by rating:</label>
                            <select wire:model.live="reviewFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="all">All Reviews</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>

                        <!-- Sorting -->
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-semibold text-gray-900">Sort by:</label>
                            <select wire:model.live="sortBy" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
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
                        <div class="bg-white rounded-lg shadow-xs border border-gray-200 p-6">
                            <!-- Review Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($review->reviewer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $review->reviewer->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $review->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>

                            <!-- Review Title -->
                            @if($review->title)
                                <h5 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h5>
                            @endif

                            <!-- Review Content -->
                            <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>

                            <!-- Helpful Count -->
                            @if($review->helpful_count > 0)
                                <div class="mt-4 flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V9a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905V9L7 20M7 20L4.5 18.5M7 20l3.5-1.5M14 10h-5V9a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2h6v-2"></path>
                                    </svg>
                                    <span>{{ $review->helpful_count }} {{ Str::plural('person', $review->helpful_count) }} found this helpful</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                            <p class="text-gray-600">Be the first to review {{ $agent->name }}</p>
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
                <div class="bg-white rounded-lg shadow-xs border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Review This Agent</h3>
                    @livewire('agent-rating-form', ['agent' => $agent])
                </div>

                <!-- Recent Properties -->
                @if($recentProperties->count() > 0)
                    <div class="bg-white rounded-lg shadow-xs border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Recent Listings</h3>
                        <div class="space-y-4">
                            @foreach($recentProperties as $property)
                                <a href="{{ route('property.show', $property) }}" wire:navigate class="block group hover:bg-gray-50 rounded-lg p-2 transition-colors">
                                    <div class="flex space-x-3">
                                        @if($property->getFirstMediaUrl('featured'))
                                            <img src="{{ $property->getFirstMediaUrl('featured', 'thumb') }}"
                                                 alt="{{ $property->title }}"
                                                 class="w-16 h-16 rounded-lg object-cover">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors truncate">
                                                {{ $property->title }}
                                            </h4>
                                            <p class="text-xs text-gray-600 mt-1">
                                                {{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs font-bold text-emerald-600 mt-1">
                                                ₦{{ number_format($property->price) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('properties.search', ['agent' => $agentProfile->id]) }}"
                               class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-3 px-4 rounded-lg transition-colors">
                                View All Listings
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>