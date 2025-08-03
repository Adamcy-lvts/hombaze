{{-- resources/views/lease-components/sections/property-info.blade.php --}}
<div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Property Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Property:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $property->title }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Address:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $property->address }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Type:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $property->propertyType->name ?? 'N/A' }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Subtype:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $property->propertySubtype->name ?? 'N/A' }}</span>
        </div>
    </div>
</div>