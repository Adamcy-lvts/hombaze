@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
        <!-- Mobile Pagination -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-6 py-3 text-sm font-semibold text-gray-400 bg-white/50 backdrop-blur-xl border border-gray-200/50 cursor-default rounded-2xl shadow-lg">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-6 py-3 text-sm font-semibold text-gray-700 bg-white/80 backdrop-blur-xl border border-gray-200/50 rounded-2xl hover:bg-white hover:text-emerald-600 focus:outline-hidden focus:ring-4 focus:ring-emerald-500/20 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-6 py-3 ml-3 text-sm font-semibold text-gray-700 bg-white/80 backdrop-blur-xl border border-gray-200/50 rounded-2xl hover:bg-white hover:text-emerald-600 focus:outline-hidden focus:ring-4 focus:ring-emerald-500/20 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Next
                </a>
            @else
                <span class="relative inline-flex items-center px-6 py-3 ml-3 text-sm font-semibold text-gray-400 bg-white/50 backdrop-blur-xl border border-gray-200/50 cursor-default rounded-2xl shadow-lg">
                    Next
                </span>
            @endif
        </div>

        <!-- Desktop Pagination -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-lg font-medium text-gray-700 bg-white/60 backdrop-blur-xl px-6 py-3 rounded-2xl border border-white/30 shadow-lg">
                    Showing
                    <span class="font-bold text-emerald-600">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="font-bold text-emerald-600">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="font-bold text-emerald-600">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-xl rounded-2xl bg-white/80 backdrop-blur-xl border border-white/30 overflow-hidden">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous">
                            <span class="relative inline-flex items-center px-4 py-3 text-sm font-semibold text-gray-400 bg-white/50 cursor-default" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-3 text-sm font-semibold text-gray-600 bg-white/80 hover:bg-linear-to-r hover:from-emerald-500 hover:to-teal-500 hover:text-white focus:z-10 focus:outline-hidden focus:ring-4 focus:ring-emerald-500/20 transition-all duration-300 group" aria-label="Previous">
                            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-3 text-sm font-semibold text-gray-500 bg-white/60 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-3 text-sm font-bold text-white bg-linear-to-r from-emerald-600 to-teal-600 cursor-default shadow-lg">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-3 text-sm font-semibold text-gray-600 bg-white/80 hover:bg-linear-to-r hover:from-emerald-50 hover:to-teal-50 hover:text-emerald-600 focus:z-10 focus:outline-hidden focus:ring-4 focus:ring-emerald-500/20 transition-all duration-300 transform hover:scale-105" aria-label="Go to page {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-3 text-sm font-semibold text-gray-600 bg-white/80 hover:bg-linear-to-r hover:from-emerald-500 hover:to-teal-500 hover:text-white focus:z-10 focus:outline-hidden focus:ring-4 focus:ring-emerald-500/20 transition-all duration-300 group" aria-label="Next">
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next">
                            <span class="relative inline-flex items-center px-4 py-3 text-sm font-semibold text-gray-400 bg-white/50 cursor-default" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
