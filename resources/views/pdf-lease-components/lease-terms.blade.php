{{-- Update your lease-terms.blade.php --}}
<div class="bg-amber-50 border border-amber-200 rounded-sm p-2 mb-3">
    <h3 class="text-xs font-semibold text-amber-800 mb-2 uppercase tracking-wide">Lease Terms</h3>
    <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs">
        <div class="flex">
            <span class="text-amber-700 font-medium w-20">Start Date:</span>
            <span class="text-gray-900">{{ $lease->start_date?->format('F j, Y') ?? 'Not set' }}</span>
        </div>
        <div class="flex">
            <span class="text-amber-700 font-medium w-20">Rent Amount:</span>
            <span class="text-gray-900 font-semibold">₦{{ number_format($lease->yearly_rent, 2) }}</span>
        </div>
        <div class="flex">
            <span class="text-amber-700 font-medium w-20">End Date:</span>
            <span class="text-gray-900">{{ $lease->end_date?->format('F j, Y') ?? 'Not set' }}</span>
        </div>
        <div class="flex">
            <span class="text-amber-700 font-medium w-20">Payment:</span>
            <span class="text-gray-900">{{ ucfirst($lease->payment_frequency) }}</span>
        </div>
    </div>
</div>
