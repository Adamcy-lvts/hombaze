<div class="min-h-screen bg-gray-50 font-sans text-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Inquiries</h1>
                <p class="text-gray-500 mt-2">Track your property inquiries and responses</p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" />
                Back to Dashboard
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Inquiries</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <x-heroicon-o-clock class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Pending</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Responded</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['responded'] }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Viewed</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['viewed'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search inquiries..."
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="statusFilter"
                            class="w-full py-2.5 px-4 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200">
                        <option value="">All Status</option>
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="viewed">Viewed</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select wire:model.live="sortBy"
                            class="w-full py-2.5 px-4 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="property_title">Property Title</option>
                        <option value="status">Status</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading.delay class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white rounded-xl p-4 shadow-lg flex items-center gap-3">
                <div class="animate-spin h-5 w-5 border-2 border-emerald-500 border-t-transparent rounded-full"></div>
                <span class="text-sm font-medium text-gray-700">Loading...</span>
            </div>
        </div>

        <!-- Inquiries List -->
        @if($inquiries->count() > 0)
            <div class="space-y-6">
                @foreach($inquiries as $inquiry)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                                <!-- Property Image -->
                                <div class="shrink-0">
                                    @if($inquiry->property && $inquiry->property->getFirstMediaUrl('featured'))
                                        <img src="{{ $inquiry->property->getFirstMediaUrl('featured') }}"
                                             alt="{{ $inquiry->property->title }}"
                                             class="w-24 h-24 object-cover rounded-xl">
                                    @else
                                        <div class="w-24 h-24 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                                            <x-heroicon-o-home class="w-10 h-10" />
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-1">
                                                {{ $inquiry->property->title ?? 'Property Not Available' }}
                                            </h3>
                                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                                <x-heroicon-o-map-pin class="w-4 h-4" />
                                                {{ $inquiry->property->address ?? 'Address not available' }}
                                            </p>
                                            <div class="flex items-center gap-3 mt-2 text-xs font-medium text-gray-500">
                                                <span>{{ $inquiry->created_at->format('M d, Y') }}</span>
                                                <span>&bull;</span>
                                                <span>{{ ucfirst($inquiry->contact_method) }}</span>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($inquiry->status === 'new') bg-amber-50 text-amber-700 border border-amber-100
                                            @elseif($inquiry->status === 'contacted') bg-emerald-50 text-emerald-700 border border-emerald-100
                                            @else bg-purple-50 text-purple-700 border border-purple-100
                                            @endif">
                                            @if($inquiry->status === 'new')
                                                <x-heroicon-o-clock class="w-3.5 h-3.5 mr-1" />
                                            @elseif($inquiry->status === 'contacted')
                                                <x-heroicon-o-check-circle class="w-3.5 h-3.5 mr-1" />
                                            @else
                                                <x-heroicon-o-eye class="w-3.5 h-3.5 mr-1" />
                                            @endif
                                            {{ ucfirst($inquiry->status) }}
                                        </span>
                                    </div>

                                    <!-- Inquiry Message -->
                                    <div class="bg-gray-50 rounded-xl p-4 mb-4 border border-gray-100">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Your Message</h4>
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $inquiry->message }}</p>
                                    </div>

                                    <!-- Response (if any) -->
                                    @if($inquiry->response)
                                        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h4 class="text-xs font-bold text-blue-700 uppercase tracking-wider">Response</h4>
                                                <span class="text-xs text-blue-500">&bull; {{ $inquiry->updated_at->format('M d, h:i A') }}</span>
                                            </div>
                                            <p class="text-sm text-blue-800 leading-relaxed">{{ $inquiry->response }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex lg:flex-col gap-2 shrink-0">
                                    @if($inquiry->status === 'new')
                                        <button wire:click="markAsRead({{ $inquiry->id }})"
                                                class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                                title="Mark as Read">
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </button>
                                    @endif

                                    @if($inquiry->property)
                                        <a href="{{ route('property.show', $inquiry->property) }}"
                                           class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                           title="View Property">
                                            <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5" />
                                        </a>
                                    @endif

                                    <button wire:click="deleteInquiry({{ $inquiry->id }})"
                                            wire:confirm="Are you sure you want to delete this inquiry?"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete Inquiry">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $inquiries->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Inquiries Found</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-8">
                    @if($search || $statusFilter)
                        No inquiries match your current filters. Try adjusting your search criteria.
                    @else
                        You haven't made any property inquiries yet. Start exploring properties to connect with agents and landlords.
                    @endif
                </p>
                @if($search || $statusFilter)
                    <button wire:click="$set('search', ''); $set('statusFilter', '')"
                            class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                        <x-heroicon-o-arrow-path class="w-5 h-5 mr-2" />
                        Clear Filters
                    </button>
                @else
                    <a href="{{ route('properties.search') }}"
                       class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors shadow-sm hover:shadow-md">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2" />
                        Browse Properties
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>