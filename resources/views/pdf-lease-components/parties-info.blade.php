{{-- Update your parties-info.blade.php --}}
<div class="grid grid-cols-2 gap-3 mb-3">
    <div class="bg-blue-50 border border-blue-100 rounded-xs p-2">
        <h3 class="text-xs font-semibold text-blue-800 mb-2 uppercase tracking-wide">Landlord (Lessor)</h3>
        <div class="space-y-0.5 text-xs">
            <div class="flex">
                <span class="text-blue-600 font-medium w-12">Name:</span>
                <span class="text-gray-900">{{ $landlord->name }}</span>
            </div>
            @if ($landlord->phone)
                <div class="flex">
                    <span class="text-blue-600 font-medium w-12">Phone:</span>
                    <span class="text-gray-900">{{ $landlord->phone }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-green-50 border border-green-100 rounded-xs p-2">
        <h3 class="text-xs font-semibold text-green-800 mb-2 uppercase tracking-wide">Tenant (Lessee)</h3>
        <div class="space-y-0.5 text-xs">
            <div class="flex">
                <span class="text-green-600 font-medium w-12">Name:</span>
                <span class="text-gray-900">{{ $tenant->name }}</span>
            </div>
            @if ($tenant->phone)
                <div class="flex">
                    <span class="text-green-600 font-medium w-12">Phone:</span>
                    <span class="text-gray-900">{{ $tenant->phone }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
