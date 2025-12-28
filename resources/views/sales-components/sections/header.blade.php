{{-- resources/views/sales-components/sections/header.blade.php --}}
<div class="text-center space-y-2">
    <h1 class="text-2xl font-bold text-gray-800">Sales Agreement</h1>
    <p class="text-sm text-gray-600">
        Property: {{ $agreement->property?->title ?? 'Property' }}
    </p>
</div>
