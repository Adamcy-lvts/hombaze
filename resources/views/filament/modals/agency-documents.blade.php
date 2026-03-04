<div class="space-y-4">
    {{-- Agency Logo --}}
    @if($logoUrl = $agency->logo_url)
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Agency Logo</h4>
            <img src="{{ $logoUrl }}" 
                 alt="Agency Logo" 
                 class="w-32 h-32 rounded-lg object-cover">
        </div>
    @endif

    {{-- CAC Document --}}
    @if($agency->getFirstMediaUrl('cac_document'))
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">CAC Document</h4>
            <a href="{{ $agency->getFirstMediaUrl('cac_document') }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <x-heroicon-o-document class="w-5 h-5 mr-2" />
                View CAC Document
            </a>
        </div>
    @endif

    {{-- License Document --}}
    @if($agency->getFirstMediaUrl('license_document'))
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">License Document</h4>
            <a href="{{ $agency->getFirstMediaUrl('license_document') }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <x-heroicon-o-document class="w-5 h-5 mr-2" />
                View License Document
            </a>
        </div>
    @endif

    {{-- Registration Details --}}
    <div class="border-t pt-4 mt-4">
        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Registration Details</h4>
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-500">RC Number</dt>
                <dd class="text-sm font-medium">{{ $agency->rc_number ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Tax ID</dt>
                <dd class="text-sm font-medium">{{ $agency->tax_id ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Phone</dt>
                <dd class="text-sm font-medium">{{ $agency->phone ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Email</dt>
                <dd class="text-sm font-medium">{{ $agency->email ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Address</dt>
                <dd class="text-sm font-medium">{{ $agency->address ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Registered</dt>
                <dd class="text-sm font-medium">{{ $agency->created_at?->format('M j, Y') ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Description --}}
    @if($agency->description)
        <div class="border-t pt-4 mt-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h4>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $agency->description }}</p>
        </div>
    @endif

    {{-- No Documents Warning --}}
    @if(!$agency->getFirstMediaUrl('logo') && !$agency->getFirstMediaUrl('cac_document') && !$agency->getFirstMediaUrl('license_document'))
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-500 mr-2" />
                <span class="text-yellow-700 dark:text-yellow-400 text-sm">No documents have been uploaded by this agency.</span>
            </div>
        </div>
    @endif
</div>
