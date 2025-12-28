{{-- resources/views/pdf-sales-components/header.blade.php --}}
<div class="bg-gray-900 text-white px-4 py-3 text-center">
    <h1 class="text-base font-bold tracking-wide">SALES AGREEMENT</h1>
    <p class="text-xs opacity-80 mt-1">{{ $record->property?->title ?? 'Property' }}</p>
</div>
