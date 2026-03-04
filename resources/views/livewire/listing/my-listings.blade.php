<div class="min-h-screen bg-gray-50 font-sans text-gray-900 pb-12">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-100/50 sticky top-0 z-30 backdrop-blur-xl bg-white/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                     <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 font-medium">
                        <a href="{{ route('landing') }}" class="hover:text-emerald-600 transition-colors">Home</a>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-900">My Listings</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('landing') }}" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors border border-gray-200/60">
                            <x-heroicon-s-arrow-left class="w-5 h-5" />
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Listings</h1>
                            <p class="text-gray-500 mt-1">Manage your properties and track their moderation status.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                     <a href="{{ route('listing.create') }}" class="group flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white pl-4 pr-1.5 py-1.5 rounded-2xl transition-all shadow-lg shadow-emerald-600/20 hover:shadow-xl hover:shadow-emerald-600/30 hover:-translate-y-0.5">
                        <span class="font-bold">List a Property</span>
                        <div class="bg-white/20 rounded-xl p-2 group-hover:bg-white/30 transition-colors">
                            <x-heroicon-m-plus class="w-5 h-5" />
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Window --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if($this->listings->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center text-center py-20 px-4 bg-white rounded-3xl border border-gray-100 shadow-sm mt-4">
                <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mb-6">
                    <x-heroicon-o-home class="w-12 h-12 text-emerald-600" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No properties listed yet</h3>
                <p class="text-gray-500 max-w-sm mb-8">Start your journey as a property owner on HomeBaze by adding your first listing today.</p>
                <a href="{{ route('listing.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                    <x-heroicon-m-plus class="w-5 h-5 mr-2" />
                    List a Property
                </a>
            </div>
        @else
            {{-- Listings Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                @foreach($this->listings as $listing)
                    <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
                        {{-- Image Header --}}
                        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden shrink-0">
                            @if($listing->getFirstMedia('featured'))
                                <img src="{{ $listing->getFeaturedImageUrl('preview') }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300 transform group-hover:scale-110 transition-transform duration-500">
                                    <x-heroicon-o-home class="w-12 h-12" />
                                </div>
                            @endif

                            {{-- Status Badge (Absolute Positioned) --}}
                            <div class="absolute top-3 right-3 flex flex-col gap-2 z-10">
                                @if($listing->moderation_status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-white/95 text-emerald-700 backdrop-blur-sm shadow-sm">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></div>
                                        Live
                                    </span>
                                @elseif($listing->moderation_status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-white/95 text-amber-600 backdrop-blur-sm shadow-sm">
                                        <x-heroicon-s-clock class="w-3.5 h-3.5 mr-1.5" />
                                        Under Review
                                    </span>
                                @elseif($listing->moderation_status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-white/95 text-red-600 backdrop-blur-sm shadow-sm">
                                        <x-heroicon-s-x-circle class="w-3.5 h-3.5 mr-1.5" />
                                        Rejected
                                    </span>
                                @endif
                                
                                @if($listing->status !== 'available')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gray-900/90 text-white backdrop-blur-sm shadow-sm text-center">
                                       {{ ucfirst($listing->status) }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Price Tag --}}
                            <div class="absolute bottom-3 left-3 flex gap-2">
                                <div class="bg-gray-900/90 backdrop-blur-md text-white px-3 py-1.5 rounded-xl text-sm font-bold shadow-lg">
                                    ₦{{ number_format($listing->price) }}
                                    @if($listing->listing_type === 'rent') <span class="text-xs font-medium text-gray-300">/yr</span> @endif
                                </div>
                                <div class="bg-white/90 text-gray-900 px-3 py-1.5 rounded-xl text-xs font-bold shadow-lg backdrop-blur-md flex items-center">
                                    {{ ucfirst($listing->listing_type) }}
                                </div>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-5 flex-1 flex flex-col">
                            {{-- Title and Location --}}
                            <div class="mb-4">
                                <h3 class="font-bold text-gray-900 text-lg line-clamp-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $listing->title }}
                                </h3>
                                <p class="text-sm text-gray-500 flex items-center mt-1.5">
                                    <x-heroicon-s-map-pin class="w-4 h-4 mr-1.5 shrink-0 text-emerald-500/70" />
                                    <span class="truncate">{{ $listing->city->name ?? 'Unknown city' }}, {{ $listing->state->name ?? 'Unknown state' }}</span>
                                </p>
                            </div>
                            
                            {{-- Key Features --}}
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600 font-medium pb-4 border-b border-gray-50 h-full content-start">
                                @if($listing->bedrooms)
                                    <span class="bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-100/50 flex flex-row items-center gap-1"><x-heroicon-o-view-columns class="w-4 h-4 opacity-50" /> {{ $listing->bedrooms }} Beds</span>
                                @endif
                                @if($listing->bathrooms)
                                    <span class="bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-100/50 flex flex-row items-center gap-1"><x-heroicon-o-inbox class="w-4 h-4 opacity-50" /> {{ $listing->bathrooms }} Baths</span>
                                @endif
                                @if($listing->plotSize)
                                    <span class="bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-100/50">{{ $listing->plotSize->display_text }}</span>
                                @elseif($listing->custom_plot_size)
                                    <span class="bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-100/50">{{ $listing->custom_plot_size }} {{ $listing->custom_plot_unit }}</span>
                                @endif
                            </div>

                            {{-- Rejection Reason --}}
                            @if($listing->moderation_status === 'rejected' && $listing->rejection_reason)
                                <div class="mt-4 p-3 bg-red-50 rounded-xl border border-red-100/50">
                                    <p class="text-xs text-red-700">
                                        <strong class="font-bold flex items-center justify-start mb-1"><x-heroicon-s-information-circle class="w-4 h-4 mr-1"/> Reason for rejection:</strong>
                                        <span class="text-red-600/90 leading-relaxed block">{{ $listing->rejection_reason }}</span>
                                    </p>
                                </div>
                            @endif

                            {{-- Footer Dates --}}
                            <div class="mt-4 flex justify-between items-center text-xs text-gray-400 font-medium">
                                <span>Listed {{ $listing->created_at->diffForHumans() }}</span>
                                <a href="#" class="text-emerald-600 hover:text-emerald-700 font-bold hover:underline transition-all">Manage listing &rarr;</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Floating Action Button (Mobile only) --}}
    @if($this->listings->isNotEmpty())
        <div class="fixed bottom-6 right-6 md:hidden z-40">
            <a href="{{ route('listing.create') }}" class="flex items-center justify-center w-14 h-14 bg-emerald-600 text-white rounded-full shadow-lg shadow-emerald-500/40 hover:bg-emerald-700 transition-transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                <x-heroicon-m-plus class="w-6 h-6" />
                <span class="sr-only">List a Property</span>
            </a>
        </div>
    @endif
</div>
