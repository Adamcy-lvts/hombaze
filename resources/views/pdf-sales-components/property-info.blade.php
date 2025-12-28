{{-- resources/views/pdf-sales-components/property-info.blade.php --}}
<section class="p-4">
    <h2 class="text-xs font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1">PROPERTY DETAILS</h2>
    <div class="grid grid-cols-2 gap-3 text-xs text-gray-700">
        <div>
            <p><span class="font-semibold">Title:</span> {{ $property?->title ?? 'N/A' }}</p>
            <p><span class="font-semibold">Address:</span> {{ $property?->address ?? 'N/A' }}</p>
            <p><span class="font-semibold">Type:</span> {{ $property?->propertyType?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p><span class="font-semibold">Area:</span> {{ $property?->area?->name ?? 'N/A' }}</p>
            <p><span class="font-semibold">City:</span> {{ $property?->city?->name ?? 'N/A' }}</p>
            <p><span class="font-semibold">Price:</span> {{ formatNaira($property?->price ?? 0) }}</p>
        </div>
    </div>
</section>
