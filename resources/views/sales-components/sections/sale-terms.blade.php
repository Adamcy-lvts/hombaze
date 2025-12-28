{{-- resources/views/sales-components/sections/sale-terms.blade.php --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 rounded-lg">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Sale Terms</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <p class="text-xs uppercase text-gray-500">Sale Price</p>
            <p class="text-base font-semibold">{{ formatNaira($agreement->sale_price ?? 0) }}</p>
        </div>
        <div>
            <p class="text-xs uppercase text-gray-500">Deposit</p>
            <p class="text-base font-semibold">{{ formatNaira($agreement->deposit_amount ?? 0) }}</p>
        </div>
        <div>
            <p class="text-xs uppercase text-gray-500">Balance</p>
            <p class="text-base font-semibold">{{ formatNaira($agreement->balance_amount ?? 0) }}</p>
        </div>
        <div>
            <p class="text-xs uppercase text-gray-500">Date Signed</p>
            <p class="text-base font-semibold">{{ $agreement->signed_date?->format('F j, Y') ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase text-gray-500">Closing Date</p>
            <p class="text-base font-semibold">{{ $agreement->closing_date?->format('F j, Y') ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase text-gray-500">Status</p>
            <p class="text-base font-semibold">{{ ucfirst($agreement->status ?? 'draft') }}</p>
        </div>
    </div>
</div>
