{{-- resources/views/pdf-sales-components/sale-terms.blade.php --}}
<section class="px-4">
    <h2 class="text-xs font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1">SALE TERMS</h2>
    <div class="grid grid-cols-3 gap-3 text-xs text-gray-700">
        <div>
            <p class="text-gray-500 uppercase">Sale Price</p>
            <p class="font-semibold">{{ formatNaira($record->sale_price ?? 0) }}</p>
        </div>
        <div>
            <p class="text-gray-500 uppercase">Deposit</p>
            <p class="font-semibold">{{ formatNaira($record->deposit_amount ?? 0) }}</p>
        </div>
        <div>
            <p class="text-gray-500 uppercase">Balance</p>
            <p class="font-semibold">{{ formatNaira($record->balance_amount ?? 0) }}</p>
        </div>
        <div>
            <p class="text-gray-500 uppercase">Signed</p>
            <p class="font-semibold">{{ $record->signed_date?->format('F j, Y') ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-gray-500 uppercase">Closing</p>
            <p class="font-semibold">{{ $record->closing_date?->format('F j, Y') ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-gray-500 uppercase">Status</p>
            <p class="font-semibold">{{ ucfirst($record->status ?? 'draft') }}</p>
        </div>
    </div>
</section>
