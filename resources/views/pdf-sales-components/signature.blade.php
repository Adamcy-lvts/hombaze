{{-- resources/views/pdf-sales-components/signature.blade.php --}}
@php
    $sellerName = $record->seller_name ?: $record->property?->owner?->name;
    $buyerName = $record->buyer_name ?: $record->buyer?->name;
@endphp

<section class="px-4 pb-4">
    <div class="grid grid-cols-2 gap-4 text-xs text-gray-700">
        <div class="border-t border-gray-300 pt-2">
            <p class="font-semibold">Seller Signature</p>
            <p class="text-gray-500">{{ $sellerName ?? 'Seller' }}</p>
        </div>
        <div class="border-t border-gray-300 pt-2">
            <p class="font-semibold">Buyer Signature</p>
            <p class="text-gray-500">{{ $buyerName ?? 'Buyer' }}</p>
        </div>
    </div>
</section>
