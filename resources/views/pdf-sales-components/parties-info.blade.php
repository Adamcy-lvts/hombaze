@php
    $sellerName = $record->seller_name ?: $record->property?->owner?->name;
    $sellerEmail = $record->seller_email ?: $record->property?->owner?->email;
    $sellerPhone = $record->seller_phone ?: $record->property?->owner?->phone;
    $sellerAddress = $record->seller_address ?: $record->property?->owner?->address;

    $buyerName = $record->buyer_name ?: $record->buyer?->name;
    $buyerEmail = $record->buyer_email ?: $record->buyer?->email;
    $buyerPhone = $record->buyer_phone ?: $record->buyer?->phone;
    $buyerAddress = $record->buyer_address ?: $record->buyer?->address;
@endphp

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
    {{-- Seller Party --}}
    <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px;">
        <div style="margin-bottom: 10px;">
            <p style="font-size: 7px; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">First Party: Seller</p>
            <p style="font-size: 11px; font-weight: 900; color: #0f172a; margin: 0;">{{ $sellerName ?? 'Authorized Seller' }}</p>
        </div>
        
        <div style="margin-bottom: 10px; font-size: 8px; color: #475569; line-height: 1.3;">
            <p style="margin: 0;">{{ $sellerEmail }}</p>
            <p style="margin: 0;">{{ $sellerPhone }}</p>
        </div>

        <div>
            <p style="font-size: 7px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Registered Address</p>
            <p style="font-size: 8px; font-weight: 600; color: #334155; margin: 0; line-height: 1.3;">{{ $sellerAddress ?? 'Address on file' }}</p>
        </div>
    </div>

    {{-- Buyer Party --}}
    <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px;">
        <div style="margin-bottom: 10px;">
            <p style="font-size: 7px; font-weight: 800; color: #16a34a; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Second Party: Buyer</p>
            <p style="font-size: 11px; font-weight: 900; color: #0f172a; margin: 0;">{{ $buyerName ?? 'Authorized Buyer' }}</p>
        </div>
        
        <div style="margin-bottom: 10px; font-size: 8px; color: #475569; line-height: 1.3;">
            <p style="margin: 0;">{{ $buyerEmail }}</p>
            <p style="margin: 0;">{{ $buyerPhone }}</p>
        </div>

        <div>
            <p style="font-size: 7px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Registered Address</p>
            <p style="font-size: 8px; font-weight: 600; color: #334155; margin: 0; line-height: 1.3;">{{ $buyerAddress ?? 'Address on file' }}</p>
        </div>
    </div>
</div>
