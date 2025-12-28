{{-- resources/views/pdf-sales-components/parties-info.blade.php --}}
@php
    $propertyOwner = $record->property?->owner;
    $sellerName = $record->seller_name ?: $propertyOwner?->name;
    $sellerEmail = $record->seller_email ?: $propertyOwner?->email;
    $buyerUser = $record->buyer;
    $buyerName = $record->buyer_name ?: $buyerUser?->name;
    $buyerEmail = $record->buyer_email ?: $buyerUser?->email;
@endphp

<section class="px-4">
    <h2 class="text-xs font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1">PARTIES</h2>
    <div class="grid grid-cols-2 gap-3 text-xs text-gray-700">
        <div class="bg-gray-50 p-2 rounded border border-gray-200">
            <p class="font-semibold text-gray-800">Seller</p>
            <p>{{ $sellerName ?? 'N/A' }}</p>
            <p class="text-gray-500">{{ $sellerEmail ?? '' }}</p>
        </div>
        <div class="bg-gray-50 p-2 rounded border border-gray-200">
            <p class="font-semibold text-gray-800">Buyer</p>
            <p>{{ $buyerName ?? 'N/A' }}</p>
            <p class="text-gray-500">{{ $buyerEmail ?? '' }}</p>
        </div>
    </div>
</section>
