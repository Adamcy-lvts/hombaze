{{-- Update your property-info.blade.php --}}
<div class="mb-3">
    <h2 class="text-sm font-medium text-gray-800 mb-2">Property Information</h2>
    <div class="bg-gray-50 border border-gray-200 rounded-xs p-2">
        <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs">
            <div class="flex">
                <span class="text-gray-600 font-medium w-16">Property:</span>
                <span class="text-gray-900">{{ $property->title }}</span>
            </div>
            <div class="flex">
                <span class="text-gray-600 font-medium w-12">Type:</span>
                <span class="text-gray-900">{{ $property->propertyType->name ?? 'N/A' }}</span>
            </div>
            <div class="flex">
                <span class="text-gray-600 font-medium w-16">Address:</span>
                <span class="text-gray-900">{{ $property->address }}</span>
            </div>
            <div class="flex">
                <span class="text-gray-600 font-medium w-12">Subtype:</span>
                <span class="text-gray-900">{{ $property->propertySubtype->name ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
</div>
