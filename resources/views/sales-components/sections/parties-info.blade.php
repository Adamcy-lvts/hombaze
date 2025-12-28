{{-- resources/views/sales-components/sections/parties-info.blade.php --}}
@php
    $propertyOwner = $agreement->property?->owner;
    $sellerName = $agreement->seller_name ?: $propertyOwner?->name;
    $sellerEmail = $agreement->seller_email ?: $propertyOwner?->email;
    $sellerPhone = $agreement->seller_phone ?: $propertyOwner?->phone;

    $buyerUser = $agreement->buyer;
    $buyerName = $agreement->buyer_name ?: $buyerUser?->name;
    $buyerEmail = $agreement->buyer_email ?: $buyerUser?->email;
    $buyerPhone = $agreement->buyer_phone ?: $buyerUser?->phone;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-3">SELLER</h3>
        <div class="space-y-2 text-sm">
            <p><span class="font-medium">Name:</span> {{ $sellerName ?? 'N/A' }}</p>
            @if($sellerEmail)
                <p><span class="font-medium">Email:</span> {{ $sellerEmail }}</p>
            @endif
            @if($sellerPhone)
                <p><span class="font-medium">Phone:</span> {{ $sellerPhone }}</p>
            @endif
        </div>
    </div>

    <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-3">BUYER</h3>
        <div class="space-y-2 text-sm">
            <p><span class="font-medium">Name:</span> {{ $buyerName ?? 'N/A' }}</p>
            @if($buyerEmail)
                <p><span class="font-medium">Email:</span> {{ $buyerEmail }}</p>
            @endif
            @if($buyerPhone)
                <p><span class="font-medium">Phone:</span> {{ $buyerPhone }}</p>
            @endif
        </div>
    </div>
</div>
