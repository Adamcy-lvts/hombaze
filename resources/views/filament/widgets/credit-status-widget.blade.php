<div class="rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Credits</div>
            <div class="text-lg font-semibold text-gray-900">
                {{ $packageName ? $packageName . ' Package' : 'No package yet' }}
            </div>
        </div>
        <div class="flex flex-wrap gap-3 text-sm text-gray-600">
            <span class="rounded-full bg-gray-100 px-3 py-1">Listing: {{ $listingBalance }}</span>
            <span class="rounded-full bg-gray-100 px-3 py-1">Featured: {{ $featuredBalance }}</span>
            @if($featuredExpiresAt)
                <span class="rounded-full bg-gray-100 px-3 py-1">
                    Featured expires {{ $featuredExpiresAt->diffForHumans() }}
                </span>
            @endif
        </div>
    </div>
</div>
