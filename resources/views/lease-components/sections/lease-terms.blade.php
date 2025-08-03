{{-- resources/views/lease-components/sections/lease-terms.blade.php --}}
<div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg mb-6">
    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-4">Lease Terms</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Start Date:</span>
            <p class="text-gray-900 dark:text-gray-100">{{ $lease->start_date?->format('F j, Y') ?? 'Not set' }}</p>
        </div>
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">End Date:</span>
            <p class="text-gray-900 dark:text-gray-100">{{ $lease->end_date?->format('F j, Y') ?? 'Not set' }}</p>
        </div>
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Annual Rent:</span>
            <p class="text-gray-900 dark:text-gray-100">₦{{ number_format($lease->monthly_rent, 2) }}</p>
        </div>
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Payment:</span>
            <p class="text-gray-900 dark:text-gray-100">{{ ucfirst($lease->payment_frequency) }}</p>
        </div>
    </div>
</div>