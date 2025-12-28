{{-- resources/views/sales-components/sections/signature.blade.php --}}
@php
    $sellerName = $agreement->seller_name ?: $agreement->property?->owner?->name;
    $buyerName = $agreement->buyer_name ?: $agreement->buyer?->name;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="border-t border-gray-300 pt-4">
        <p class="text-sm font-medium text-gray-700">Seller Signature</p>
        <p class="text-xs text-gray-500 mt-1">{{ $sellerName ?? 'Seller' }}</p>
    </div>
    <div class="border-t border-gray-300 pt-4">
        <p class="text-sm font-medium text-gray-700">Buyer Signature</p>
        <p class="text-xs text-gray-500 mt-1">{{ $buyerName ?? 'Buyer' }}</p>
    </div>
</div>
