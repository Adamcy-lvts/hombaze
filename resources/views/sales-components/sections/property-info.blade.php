{{-- resources/views/sales-components/sections/property-info.blade.php --}}
<div class="bg-gray-50 dark:bg-gray-900/30 p-6 rounded-lg">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Property Details</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <p><span class="font-medium">Title:</span> {{ $property?->title ?? 'N/A' }}</p>
            <p><span class="font-medium">Address:</span> {{ $property?->address ?? 'N/A' }}</p>
            <p><span class="font-medium">Type:</span> {{ $property?->propertyType?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p><span class="font-medium">Location:</span> {{ $property?->area?->name ?? 'N/A' }}, {{ $property?->city?->name ?? 'N/A' }}</p>
            <p><span class="font-medium">State:</span> {{ $property?->state?->name ?? 'N/A' }}</p>
            <p><span class="font-medium">Listing Price:</span> {{ formatNaira($property?->price ?? 0) }}</p>
        </div>
    </div>
</div>
