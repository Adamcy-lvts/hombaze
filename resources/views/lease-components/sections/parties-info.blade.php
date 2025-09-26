{{-- resources/views/lease-components/sections/parties-info.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-3">LANDLORD (Lessor)</h3>
        <div class="space-y-2">
            <p><span class="font-medium">Name:</span> {{ $landlord->name }}</p>
            @if($landlord->phone)
                <p><span class="font-medium">Phone:</span> {{ $landlord->phone }}</p>
            @endif
        </div>
    </div>
    
    <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-3">TENANT (Lessee)</h3>
        <div class="space-y-2">
            <p><span class="font-medium">Name:</span> {{ $tenant->name }}</p>
            @if($tenant->phone)
                <p><span class="font-medium">Phone:</span> {{ $tenant->phone }}</p>
            @endif
        </div>
    </div>
</div>