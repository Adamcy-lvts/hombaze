{{-- Landlord Rent Receipt View --}}
<x-filament::page>
    <div>
        <!-- Download Actions Buttons -->
        <div class="flex flex-wrap justify-end gap-4 mb-6">
            <x-filament::button color="primary" wire:click="downloadPdf" icon="heroicon-o-document-arrow-down"
                class="filament-page-button-action">
                Download PDF
            </x-filament::button>

            <x-filament::button color="success" wire:click="downloadPng" icon="heroicon-o-photo"
                class="filament-page-button-action">
                Download PNG
            </x-filament::button>
        </div>

        <!-- Receipt - Premium Horizontal Layout -->
        <div id="receipt-container"
            class="bg-linear-to-r from-slate-50 to-slate-100 p-8 shadow-2xl rounded-lg border border-gray-200 max-w-7xl mx-auto relative overflow-hidden">
            <!-- Premium subtle background pattern -->
            <div
                class="absolute inset-0 opacity-5 pattern-diagonal-lines pattern-gray-700 pattern-size-2 pattern-bg-transparent">
            </div>

            <!-- Header Row - Improved Layout -->
            <div class="flex justify-between items-start mb-6 relative z-10">
                <!-- Left: Company Info -->
                <div class="flex items-center space-x-4 flex-1">
                    @php
                        $inlineLogo = function (string $relativePath): ?string {
                            $fullPath = public_path($relativePath);
                            if (!is_file($fullPath)) {
                                return null;
                            }
                            $contents = file_get_contents($fullPath);
                            if ($contents === false) {
                                return null;
                            }
                            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                            $mime = $extension === 'svg' ? 'image/svg+xml' : "image/{$extension}";
                            return 'data:' . $mime . ';base64,' . base64_encode($contents);
                        };
                        $inlineStorageLogo = function (?string $storagePath): ?string {
                            if (!$storagePath) {
                                return null;
                            }
                            $fullPath = storage_path('app/public/' . ltrim($storagePath, '/'));
                            if (!is_file($fullPath)) {
                                return null;
                            }
                            $contents = file_get_contents($fullPath);
                            if ($contents === false) {
                                return null;
                            }
                            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                            $mime = $extension === 'svg' ? 'image/svg+xml' : "image/{$extension}";
                            return 'data:' . $mime . ';base64,' . base64_encode($contents);
                        };

                        $businessInfo = null;
                        $businessLogo = null;
                        $businessInitials = 'HB';

                        // Business detection hierarchy:
                        // 1. Agency (if property belongs to an agency)
                        // 2. Property Owner's Company (if it's a company)
                        // 3. Independent Agent's business (if managed by independent agent)
// 4. Landlord's business info (from user preferences - future)
                        // 5. HomeBaze fallback

                        // 1. Check for Agency
                        if ($receipt->lease && $receipt->lease->property && $receipt->lease->property->agency) {
                            $agency = $receipt->lease->property->agency;
                            $businessInfo = [
                                'name' => $agency->name,
                                'email' => $agency->email ?? 'support@homebaze.com',
                                'phone' => $agency->phone ?? '+234 (0) 123-456-7890',
                                'website' => $agency->website ?? 'www.homebaze.com',
                                'tagline' => 'Real Estate Agency',
                            ];
                            $businessLogo = $inlineStorageLogo($agency->logo) ?? $inlineLogo('images/app-logo.svg');
                            $businessInitials = strtoupper(substr($agency->name, 0, 2));
                        }
                        // 2. Check for Property Owner Company
                        elseif (
                            $receipt->lease &&
                            $receipt->lease->property &&
                            $receipt->lease->property->owner &&
                            $receipt->lease->property->owner->type === 'company' &&
                            $receipt->lease->property->owner->company_name
                        ) {
                            $owner = $receipt->lease->property->owner;
                            $businessInfo = [
                                'name' => $owner->company_name,
                                'email' => $owner->email ?? ($receipt->landlord->email ?? 'support@homebaze.com'),
                                'phone' => $owner->phone ?? ($receipt->landlord->phone ?? '+234 (0) 123-456-7890'),
                                'website' => $owner->website ?? null,
                                'tagline' => null, // No tagline for property owner companies
                            ];
                            $businessInitials = strtoupper(substr($owner->company_name, 0, 2));
                        }
                        // 3. Check for Independent Agent's business (if property has independent agent)
elseif (
    $receipt->lease &&
    $receipt->lease->property &&
    $receipt->lease->property->agent_id &&
    !$receipt->lease->property->agency_id
) {
    // Independent agent - for now use agent's name as business name
                            $agentUser = \App\Models\User::find($receipt->lease->property->agent_id);
                            if ($agentUser) {
                                $businessInfo = [
                                    'name' => $agentUser->name . ' Real Estate',
                                    'email' => $agentUser->email,
                                    'phone' => $agentUser->phone ?? '+234 (0) 123-456-7890',
                                    'website' => 'www.homebaze.com',
                                    'tagline' => 'Independent Real Estate Agent',
                                ];
                                $businessInitials = strtoupper(substr($agentUser->name, 0, 2));
                            }
                        }

                        // Default fallback to HomeBaze
                        if (!$businessInfo) {
                            $businessInfo = [
                                'name' => 'HomeBaze Property',
                                'email' => 'support@homebaze.com',
                                'phone' => '+234 (0) 123-456-7890',
                                'website' => 'www.homebaze.com',
                                'tagline' => 'Management System',
                            ];
                            $businessLogo = $inlineLogo('images/app-logo.svg');
                        }

                        $landlordInfo = $receipt->landlord;

                        $isPropertyOwnerCompany =
                            $receipt->lease &&
                            $receipt->lease->property &&
                            $receipt->lease->property->owner &&
                            $receipt->lease->property->owner->type === 'company' &&
                            $receipt->lease->property->owner->company_name;
                    @endphp

                    @if ($businessLogo)
                        <img src="{{ $businessLogo }}" alt="{{ $businessInfo['name'] }} Logo"
                            class="w-20 drop-shadow-md">
                    @else
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center">
                            <span class="text-lg font-bold text-indigo-600">{{ $businessInitials }}</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">{{ $businessInfo['name'] }}</h2>
                        @if ($businessInfo['tagline'])
                            <p class="text-sm text-gray-600">{{ $businessInfo['tagline'] }}</p>
                        @endif
                        @if ($isPropertyOwnerCompany)
                            <!-- Show PropertyOwner company contacts under company name -->
                            <div class="text-sm text-gray-600 mt-2 space-y-1">
                                <p class="font-medium">{{ $businessInfo['email'] }}</p>
                                <p>{{ $businessInfo['phone'] }}</p>
                                @if ($businessInfo['website'])
                                    <p>{{ $businessInfo['website'] }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Center: Contact Details (only for non-PropertyOwner companies) -->
                @if (!$isPropertyOwnerCompany)
                    <div class="text-center text-gray-600 text-sm flex-1">
                        <p class="font-semibold">{{ $businessInfo['email'] }}</p>
                        @if ($businessInfo['website'])
                            <p>{{ $businessInfo['website'] }}</p>
                        @endif
                        <p class="text-xs">{{ $businessInfo['phone'] }}</p>
                    </div>
                @endif

                <!-- Right: Receipt Number (Always at far right) -->
                <div class="text-right">
                    <div class="bg-indigo-100 px-3 py-2 rounded-sm shadow-xs border border-indigo-200">
                        <p class="text-xs text-gray-600">Receipt No:</p>
                        <p class="text-sm font-bold text-indigo-700">{{ $receipt->receipt_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Area - Optimized Layout -->
            <!-- Top Row: Basic Information (4 columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow-xs border border-gray-200">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Received From:</p>
                    <p class="text-lg text-gray-800 font-medium">{{ $receipt->tenant->name ?? 'N/A' }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-xs border border-gray-200">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Payment Date:</p>
                    <p class="text-lg text-gray-800 font-medium">
                        {{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('F j, Y') : now()->format('F j, Y') }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-xs border border-gray-200">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Payment For:</p>
                    <p class="text-lg text-gray-800 font-medium">{{ $receipt->payment_period ?? 'Rent Payment' }}</p>
                </div>
                <div
                    class="bg-linear-to-r from-indigo-50 to-blue-50 p-4 rounded-lg shadow-xs border-2 border-indigo-200">
                    <p class="text-sm font-semibold text-indigo-700 mb-2">Total Amount</p>
                    <p class="text-2xl font-bold text-indigo-700">₦{{ number_format($receipt->amount, 2) }}</p>
                </div>
            </div>

            <!-- Landlord Information -->
            <div class="bg-white p-4 rounded-lg shadow-xs border border-gray-200 mb-6">
                <p class="text-sm font-semibold text-gray-600 mb-2">Landlord Details</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
                    <div>
                        <span class="text-xs uppercase tracking-wide text-gray-500">Name</span>
                        <p class="font-medium">{{ $landlordInfo->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-wide text-gray-500">Email</span>
                        <p class="font-medium">{{ $landlordInfo->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-wide text-gray-500">Phone</span>
                        <p class="font-medium">{{ $landlordInfo->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Second Row: Property Information (Full Width) -->
            @if ($receipt->lease && $receipt->lease->property)
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500 shadow-xs mb-6">
                    <p class="font-semibold text-blue-700 mb-2">Property Details</p>
                    <p class="text-gray-800 font-medium text-lg">{{ $receipt->lease->property->title }}</p>
                    @if ($receipt->lease->property->address)
                        <p class="text-sm text-gray-600 mt-1">{{ $receipt->lease->property->address }}</p>
                    @endif
                    @if ($receipt->lease->property->city || $receipt->lease->property->state)
                        <p class="text-sm text-gray-600">
                            @if ($receipt->lease->property->city)
                                {{ $receipt->lease->property->city->name }}
                            @endif
                            @if ($receipt->lease->property->city && $receipt->lease->property->state)
                                ,
                            @endif
                            @if ($receipt->lease->property->state)
                                {{ $receipt->lease->property->state->name }}
                            @endif
                        </p>
                    @endif
                    @if ($receipt->lease->property->bedrooms)
                        <p class="text-sm text-gray-600 mt-2">
                            <span class="font-medium">{{ $receipt->lease->property->bedrooms }}
                                Bedroom{{ $receipt->lease->property->bedrooms > 1 ? 's' : '' }}</span>
                            @if ($receipt->lease->property->bathrooms)
                                • {{ $receipt->lease->property->bathrooms }}
                                Bathroom{{ $receipt->lease->property->bathrooms > 1 ? 's' : '' }}
                            @endif
                        </p>
                    @endif
                </div>
            @endif

            <!-- Third Row: Lease Dates & Payment Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Lease Start Date -->
                @if ($receipt->lease)
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500 shadow-xs">
                        <p class="font-semibold text-green-700 mb-2">Lease Start</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $receipt->lease->start_date ? \Carbon\Carbon::parse($receipt->lease->start_date)->format('M j, Y') : 'N/A' }}
                        </p>
                    </div>

                    <!-- Lease End Date -->
                    <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500 shadow-xs">
                        <p class="font-semibold text-red-700 mb-2">Lease End</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $receipt->lease->end_date ? \Carbon\Carbon::parse($receipt->lease->end_date)->format('M j, Y') : 'N/A' }}
                        </p>
                    </div>
                @endif

                <!-- Payment Breakdown -->
                <div class="bg-white p-4 rounded-lg shadow-xs border border-gray-200">
                    <p class="font-semibold text-gray-700 mb-2">Payment Breakdown</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-green-600">
                            <span>Deposit:</span>
                            <span class="font-medium">₦{{ number_format($receipt->deposit ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600">
                            <span>Balance Due:</span>
                            <span class="font-medium">₦{{ number_format($receipt->balance_due ?? 0, 2) }}</span>
                        </div>
                        @if ($receipt->lease && $receipt->lease->security_deposit)
                            <div class="flex justify-between text-yellow-600">
                                <span>Security Deposit:</span>
                                <span
                                    class="font-medium">₦{{ number_format($receipt->lease->security_deposit, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Fourth Row: Amount in Words & Payment Method -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Amount in Words (spans 2 columns) -->
                <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg shadow-xs border border-gray-200">
                    <p class="font-semibold text-gray-600 mb-2">Amount in Words:</p>
                    <p class="text-gray-800 italic font-medium">{{ $amountInWords }}</p>
                </div>

                <!-- Payment Method -->
                <div class="bg-white p-4 rounded-lg shadow-xs border border-gray-200">
                    <p class="font-semibold text-gray-700 mb-2">Payment Method</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div
                            class="flex items-center space-x-2 text-sm {{ $receipt->payment_method == 'cash' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                            <div
                                class="w-4 h-4 border-2 border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'cash' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if ($receipt->payment_method == 'cash')
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span>Cash</span>
                        </div>
                        <div
                            class="flex items-center space-x-2 text-sm {{ $receipt->payment_method == 'transfer' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                            <div
                                class="w-4 h-4 border-2 border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'transfer' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if ($receipt->payment_method == 'transfer')
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span>Transfer</span>
                        </div>
                        <div
                            class="flex items-center space-x-2 text-sm {{ $receipt->payment_method == 'pos' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                            <div
                                class="w-4 h-4 border-2 border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'pos' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if ($receipt->payment_method == 'pos')
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span>POS</span>
                        </div>
                        <div
                            class="flex items-center space-x-2 text-sm {{ $receipt->payment_method == 'card' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                            <div
                                class="w-4 h-4 border-2 border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'card' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if ($receipt->payment_method == 'card')
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span>Card</span>
                        </div>
                    </div>
                </div>

            </div>


            <!-- QR Code positioned at bottom right -->
            <div class="relative">
                <div class="absolute bottom-0 right-0 mb-4">
                    <div class="bg-white p-3 rounded-lg shadow-xs border border-gray-300 inline-block">
                        @php
                            $qrCode = $this->generateQrCode();
                        @endphp
                        @if ($qrCode)
                            {!! $qrCode !!}
                        @else
                            <svg width="80" height="80" viewBox="0 0 80 80" class="border">
                                <rect width="80" height="80" fill="white" />
                                <g fill="black">
                                    <rect x="10" y="10" width="15" height="15" />
                                    <rect x="55" y="10" width="15" height="15" />
                                    <rect x="10" y="55" width="15" height="15" />
                                    <rect x="35" y="35" width="10" height="10" />
                                </g>
                                <text x="40" y="45" text-anchor="middle" font-size="6" fill="black">QR</text>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 pt-4 border-t border-gray-200 text-center text-gray-500 text-sm">
                <p>Thank you for your timely payment!</p>
                <p class="mt-1">This receipt was generated electronically and is valid without a physical signature.
                </p>
                <p class="mt-1 font-semibold">Generated via HomeBaze Property Management System | <strong>Powered by
                        DevCentric</strong></p>
            </div>
        </div>
    </div>

    <style>
        .pattern-diagonal-lines {
            background-image: repeating-linear-gradient(45deg, currentColor 0, currentColor 1px, transparent 0, transparent 50%);
            background-size: 10px 10px;
        }
    </style>
</x-filament::page>
