<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Inquiries</h1>
                <p class="text-gray-600 mt-1">Track your property inquiries and responses</p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg text-gray-700 hover:bg-white transition-all duration-200 shadow-sm">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                Back to Dashboard
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/50 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-500/10">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Inquiries</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/50 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-500/10">
                        <x-heroicon-o-clock class="w-6 h-6 text-yellow-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/50 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-500/10">
                        <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Responded</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['responded'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/50 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-purple-500/10">
                        <x-heroicon-o-eye class="w-6 h-6 text-purple-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Viewed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['viewed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/50 shadow-sm mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search inquiries..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="statusFilter"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="property_title">Property Title</option>
                        <option value="status">Status</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading.delay class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <div class="animate-spin h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                <span class="text-gray-700">Loading...</span>
            </div>
        </div>

        <!-- Inquiries List -->
        @if($inquiries->count() > 0)
            <div class="space-y-6">
                @foreach($inquiries as $inquiry)
                    <div class="bg-white/70 backdrop-blur-sm rounded-xl border border-white/50 shadow-sm overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Property Info -->
                                    <div class="flex items-start space-x-4 mb-4">
                                        @if($inquiry->property && $inquiry->property->getFirstMediaUrl('featured'))
                                            <img src="{{ $inquiry->property->getFirstMediaUrl('featured') }}"
                                                 alt="{{ $inquiry->property->title }}"
                                                 class="w-16 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <x-heroicon-o-home class="w-8 h-8 text-gray-400" />
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                {{ $inquiry->property->title ?? 'Property Not Available' }}
                                            </h3>
                                            <p class="text-gray-600 text-sm mb-2">
                                                {{ $inquiry->property->address ?? 'Address not available' }}
                                            </p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span>{{ $inquiry->created_at->format('M d, Y') }}</span>
                                                <span>•</span>
                                                <span>{{ ucfirst($inquiry->contact_method) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Inquiry Message -->
                                    <div class="bg-gray-50/50 rounded-lg p-4 mb-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Your Message:</h4>
                                        <p class="text-gray-700">{{ $inquiry->message }}</p>
                                    </div>

                                    <!-- Response (if any) -->
                                    @if($inquiry->response)
                                        <div class="bg-blue-50/50 rounded-lg p-4 mb-4">
                                            <h4 class="text-sm font-medium text-blue-700 mb-2">Response:</h4>
                                            <p class="text-blue-700">{{ $inquiry->response }}</p>
                                            <p class="text-xs text-blue-600 mt-2">
                                                Responded on {{ $inquiry->updated_at->format('M d, Y \a\t h:i A') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Status & Actions -->
                                <div class="flex flex-col items-end space-y-3">
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($inquiry->status === 'new') bg-yellow-100 text-yellow-800
                                        @elseif($inquiry->status === 'contacted') bg-green-100 text-green-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        @if($inquiry->status === 'new')
                                            <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                        @elseif($inquiry->status === 'contacted')
                                            <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        @else
                                            <x-heroicon-o-eye class="w-3 h-3 mr-1" />
                                        @endif
                                        {{ ucfirst($inquiry->status) }}
                                    </span>

                                    <!-- Actions -->
                                    <div class="flex space-x-2">
                                        @if($inquiry->status === 'new')
                                            <button wire:click="markAsRead({{ $inquiry->id }})"
                                                    class="p-2 text-gray-400 hover:text-purple-600 transition-colors">
                                                <x-heroicon-o-eye class="w-4 h-4" />
                                            </button>
                                        @endif

                                        @if($inquiry->property)
                                            <a href="{{ route('property.show', $inquiry->property) }}"
                                               class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                            </a>
                                        @endif

                                        <button wire:click="deleteInquiry({{ $inquiry->id }})"
                                                onclick="return confirm('Are you sure you want to delete this inquiry?')"
                                                class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
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
            <div class="bg-white/70 backdrop-blur-sm rounded-xl border border-white/50 shadow-sm p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 mx-auto mb-4 p-3 bg-gray-100 rounded-full">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Inquiries Found</h3>
                    <p class="text-gray-600 mb-6">
                        @if($search || $statusFilter)
                            No inquiries match your current filters. Try adjusting your search criteria.
                        @else
                            You haven't made any property inquiries yet. Start exploring properties to connect with agents and landlords.
                        @endif
                    </p>
                    @if($search || $statusFilter)
                        <button wire:click="$set('search', ''); $set('statusFilter', '')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                            Clear Filters
                        </button>
                    @else
                        <a href="{{ route('properties.search') }}"
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2" />
                            Browse Properties
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>