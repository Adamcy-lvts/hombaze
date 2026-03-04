    {{-- Profile Photo --}}
    @if($photoUrl = $owner->profile_photo_url)
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Profile Photo</h4>
            <img src="{{ $photoUrl }}" 
                 alt="Profile Photo" 
                 class="w-32 h-32 rounded-lg object-cover">
        </div>
    @endif

    {{-- ID Document --}}
    @if($idUrl = $owner->id_document_url)
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">ID Document</h4>
            <a href="{{ $idUrl }}" 
               target="_blank"
               download
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                Download ID Document
            </a>
        </div>
    @endif

    {{-- Proof of Address --}}
    @if($addressUrl = $owner->proof_of_address_url)
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Proof of Address</h4>
            <a href="{{ $addressUrl }}" 
               target="_blank"
               download
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                Download Proof of Address
            </a>
        </div>
    @endif

    {{-- Registration Details --}}
    <div class="border-t pt-4 mt-4">
        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Registration Details</h4>
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-500">Owner Type</dt>
                <dd class="text-sm font-medium">{{ ucfirst($owner->type ?? 'individual') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">NIN Number</dt>
                <dd class="text-sm font-medium">{{ $owner->nin_number ?? 'Not provided' }}</dd>
            </div>
            @if($owner->type === 'individual')
            <div>
                <dt class="text-xs text-gray-500">Full Name</dt>
                <dd class="text-sm font-medium">{{ ($owner->first_name ?? '') . ' ' . ($owner->last_name ?? '') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Date of Birth</dt>
                <dd class="text-sm font-medium">{{ $owner->date_of_birth?->format('M j, Y') ?? 'Not provided' }}</dd>
            </div>
            @else
            <div>
                <dt class="text-xs text-gray-500">Company Name</dt>
                <dd class="text-sm font-medium">{{ $owner->company_name ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Tax ID</dt>
                <dd class="text-sm font-medium">{{ $owner->tax_id ?? 'Not provided' }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-xs text-gray-500">Phone</dt>
                <dd class="text-sm font-medium">{{ $owner->phone ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Email</dt>
                <dd class="text-sm font-medium">{{ $owner->email ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Address</dt>
                <dd class="text-sm font-medium">{{ $owner->address ?? 'Not provided' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Registered</dt>
                <dd class="text-sm font-medium">{{ $owner->created_at?->format('M j, Y') ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Notes --}}
    @if($owner->notes)
        <div class="border-t pt-4 mt-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h4>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $owner->notes }}</p>
        </div>
    @endif

    {{-- No Documents Warning --}}
    @if(!$owner->profile_photo && !$owner->id_document && !$owner->proof_of_address)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-500 mr-2" />
                <span class="text-yellow-700 dark:text-yellow-400 text-sm">No documents have been uploaded by this property owner.</span>
            </div>
        </div>
    @endif
</div>
