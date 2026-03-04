<div class="space-y-4">
    {{-- Profile Photo --}}
    @if($agent->getFirstMediaUrl('profile_photo'))
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Profile Photo</h4>
            <img src="{{ $agent->getFirstMediaUrl('profile_photo') }}" 
                 alt="Profile Photo" 
                 class="w-32 h-32 rounded-lg object-cover">
        </div>
    @endif

    {{-- ID Document --}}
    @if($agent->getFirstMediaUrl('id_document'))
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">ID Document</h4>
            <a href="{{ $agent->getFirstMediaUrl('id_document') }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <x-heroicon-o-document class="w-5 h-5 mr-2" />
                View ID Document
            </a>
        </div>
    @endif

    {{-- License Document --}}
    @if($agent->getFirstMediaUrl('license_document'))
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">License Document</h4>
            <a href="{{ $agent->getFirstMediaUrl('license_document') }}" 
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
                <dt class="text-xs text-gray-500">License Number</dt>
                <dd class="text-sm font-medium">{{ $agent->license_number ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Years Experience</dt>
                <dd class="text-sm font-medium">{{ $agent->years_experience ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Phone</dt>
                <dd class="text-sm font-medium">{{ $agent->phone ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Registered</dt>
                <dd class="text-sm font-medium">{{ $agent->created_at?->format('M j, Y') ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Bio --}}
    @if($agent->bio)
        <div class="border-t pt-4 mt-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Bio</h4>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $agent->bio }}</p>
        </div>
    @endif

    {{-- No Documents Warning --}}
    @if(!$agent->getFirstMediaUrl('profile_photo') && !$agent->getFirstMediaUrl('id_document') && !$agent->getFirstMediaUrl('license_document'))
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-500 mr-2" />
                <span class="text-yellow-700 dark:text-yellow-400 text-sm">No documents have been uploaded by this agent.</span>
            </div>
        </div>
    @endif
</div>
