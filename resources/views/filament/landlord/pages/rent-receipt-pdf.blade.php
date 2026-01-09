<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Receipt {{ $receipt->receipt_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {!! isset($customCss) ? $customCss : '' !!}
    <style>
        .pattern-diagonal-lines {
            background-image: repeating-linear-gradient(45deg, currentColor 0, currentColor 1px, transparent 0, transparent 50%);
            background-size: 10px 10px;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div id="capture-area"
        class="bg-linear-to-r from-slate-50 to-slate-100 p-4 shadow-2xl rounded-lg border border-gray-200 max-w-7xl mx-auto relative overflow-hidden">
        <!-- Premium subtle background pattern -->
        <div
            class="absolute inset-0 opacity-5 pattern-diagonal-lines pattern-gray-700 pattern-size-2 pattern-bg-transparent">
        </div>

        <!-- Header Row - Improved Layout -->
        <div class="flex justify-between items-start mb-3 relative z-10">
            <!-- Left: Company Info -->
            <div class="flex items-center space-x-2 flex-1">
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
                            'email' => $agency->email ?? 'support@homebaze.live',
                            'phone' => $agency->phone ?? '+2347071940611',
                            'website' => $agency->website ?? 'www.homebaze.live',
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
                            'email' => $owner->email ?? ($receipt->landlord->email ?? 'support@homebaze.live'),
                            'phone' => $owner->phone ?? ($receipt->landlord->phone ?? '+2347071940611'),
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
                                'phone' => $agentUser->phone ?? '+2347071940611',
                                'website' => 'www.homebaze.live',
                                'tagline' => 'Independent Real Estate Agent',
                            ];
                            $businessInitials = strtoupper(substr($agentUser->name, 0, 2));
                        }
                    }

                    // Default fallback to HomeBaze
                    if (!$businessInfo) {
                        $businessInfo = [
                            'name' => 'HomeBaze Property',
                            'email' => 'support@homebaze.live',
                            'phone' => '+2347071940611',
                            'website' => 'www.homebaze.live',
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
                    <img src="{{ $businessLogo }}" alt="{{ $businessInfo['name'] }} Logo" class="w-12 drop-shadow-md">
                @else
                    <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center">
                        <span class="text-sm font-bold text-indigo-600">{{ $businessInitials }}</span>
                    </div>
                @endif
                <div>
                    <h2 class="text-sm font-bold text-gray-800">{{ $businessInfo['name'] }}</h2>
                    @if ($businessInfo['tagline'])
                        <p class="text-xs text-gray-600">{{ $businessInfo['tagline'] }}</p>
                    @endif
                    @if ($isPropertyOwnerCompany)
                        <!-- Show PropertyOwner company contacts under company name -->
                        <div class="text-xs text-gray-600 mt-1 space-y-0.5">
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
                <div class="text-center text-gray-600 text-xs flex-1">
                    <p class="font-semibold">{{ $businessInfo['email'] }}</p>
                    @if ($businessInfo['website'])
                        <p>{{ $businessInfo['website'] }}</p>
                    @endif
                    <p class="text-xs">{{ $businessInfo['phone'] }}</p>
                </div>
            @endif

            <!-- Right: Receipt Number (Always at far right) -->
            <div class="text-right">
                <div class="bg-indigo-100 px-2 py-1 rounded-sm shadow-xs border border-indigo-200">
                    <p class="text-xs text-gray-600">Receipt No:</p>
                    <p class="text-xs font-bold text-indigo-700">{{ $receipt->receipt_number }}</p>
                </div>
            </div>
        </div>

        <!-- Main Content Area - Optimized Layout -->
        <!-- Top Row: Basic Information (4 columns) -->
            <div class="grid grid-cols-4 gap-3 mb-3">
                <div class="bg-white p-2 rounded-lg shadow-xs border border-gray-200">
                    <p class="text-xs font-semibold text-gray-600 mb-1">Received From:</p>
                    <p class="text-sm text-gray-800 font-medium">{{ $receipt->tenant->name ?? 'N/A' }}</p>
                </div>
            <div class="bg-white p-2 rounded-lg shadow-xs border border-gray-200">
                <p class="text-xs font-semibold text-gray-600 mb-1">Payment Date:</p>
                <p class="text-sm text-gray-800 font-medium">
                    {{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('F j, Y') : now()->format('F j, Y') }}
                </p>
            </div>
            <div class="bg-white p-2 rounded-lg shadow-xs border border-gray-200">
                <p class="text-xs font-semibold text-gray-600 mb-1">Payment For:</p>
                <p class="text-sm text-gray-800 font-medium">{{ $receipt->payment_period ?? 'Rent Payment' }}</p>
            </div>
                <div class="bg-linear-to-r from-indigo-50 to-blue-50 p-2 rounded-lg shadow-xs border-2 border-indigo-200">
                    <p class="text-xs font-semibold text-indigo-700 mb-1">Total Amount</p>
                    <p class="text-lg font-bold text-indigo-700">₦{{ number_format($receipt->amount, 2) }}</p>
                </div>
            </div>

        <!-- Landlord Information -->
        <div class="bg-white p-2 rounded-lg shadow-xs border border-gray-200 mb-3">
            <p class="text-xs font-semibold text-gray-600 mb-1">Landlord Details</p>
            <div class="grid grid-cols-3 gap-2 text-xs text-gray-700">
                <div>
                    <span class="text-[10px] uppercase tracking-wide text-gray-500">Name</span>
                    <p class="font-medium">{{ $landlordInfo->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase tracking-wide text-gray-500">Email</span>
                    <p class="font-medium">{{ $landlordInfo->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase tracking-wide text-gray-500">Phone</span>
                    <p class="font-medium">{{ $landlordInfo->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Second Row: Property Information (Full Width) -->
        @if ($receipt->lease && $receipt->lease->property)
            <div class="bg-blue-50 p-2 rounded-lg border-l-4 border-blue-500 shadow-xs mb-3">
                <p class="font-semibold text-blue-700 mb-1 text-xs">Property Details</p>
                <p class="text-gray-800 font-medium text-sm">{{ $receipt->lease->property->title }}</p>
                @if ($receipt->lease->property->address)
                    <p class="text-xs text-gray-600 mt-0.5">{{ $receipt->lease->property->address }}</p>
                @endif
                @if ($receipt->lease->property->city || $receipt->lease->property->state)
                    <p class="text-xs text-gray-600">
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
                    <p class="text-xs text-gray-600 mt-0.5">
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
        <div class="grid grid-cols-3 gap-3 mb-3">
            <!-- Lease Start Date -->
            @if ($receipt->lease)
                <div class="bg-green-50 p-2 rounded-lg border-l-4 border-green-500 shadow-xs">
                    <p class="font-semibold text-green-700 mb-1 text-xs">Lease Start</p>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $receipt->lease->start_date ? \Carbon\Carbon::parse($receipt->lease->start_date)->format('M j, Y') : 'N/A' }}
                    </p>
                </div>

                <!-- Lease End Date -->
                <div class="bg-red-50 p-2 rounded-lg border-l-4 border-red-500 shadow-xs">
                    <p class="font-semibold text-red-700 mb-1 text-xs">Lease End</p>
                    <p class="text-sm font-medium text-gray-800">
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
                            <span class="font-medium">₦{{ number_format($receipt->lease->security_deposit, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Fourth Row: Amount in Words & Payment Method -->
        <div class="grid grid-cols-4 gap-3 mb-3">
            <!-- Amount in Words (spans 2 columns) -->
            <div class="col-span-2 bg-gray-50 p-2 rounded-lg shadow-xs border border-gray-200">
                <p class="font-semibold text-gray-600 mb-1 text-xs">Amount in Words:</p>
                <p class="text-gray-800 italic font-medium text-sm">{{ $amountInWords }}</p>
            </div>

            <!-- Payment Method -->
            <div class="bg-white p-2 rounded-lg shadow-xs border border-gray-200">
                <p class="font-semibold text-gray-700 mb-1 text-xs">Payment Method</p>
                <div class="grid grid-cols-2 gap-1">
                    <div
                        class="flex items-center space-x-1 text-xs {{ $receipt->payment_method == 'cash' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                        <div
                            class="w-3 h-3 border border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'cash' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                            @if ($receipt->payment_method == 'cash')
                                <svg class="w-2 h-2 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                        <span>Cash</span>
                    </div>
                    <div
                        class="flex items-center space-x-1 text-xs {{ $receipt->payment_method == 'transfer' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                        <div
                            class="w-3 h-3 border border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'transfer' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                            @if ($receipt->payment_method == 'transfer')
                                <svg class="w-2 h-2 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                        <span>Transfer</span>
                    </div>
                    <div
                        class="flex items-center space-x-1 text-xs {{ $receipt->payment_method == 'pos' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                        <div
                            class="w-3 h-3 border border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'pos' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                            @if ($receipt->payment_method == 'pos')
                                <svg class="w-2 h-2 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                        <span>POS</span>
                    </div>
                    <div
                        class="flex items-center space-x-1 text-xs {{ $receipt->payment_method == 'card' ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                        <div
                            class="w-3 h-3 border border-gray-500 rounded-xs flex items-center justify-center {{ $receipt->payment_method == 'card' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                            @if ($receipt->payment_method == 'card')
                                <svg class="w-2 h-2 text-white" viewBox="0 0 20 20" fill="currentColor">
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
            <div class="absolute bottom-0 right-0 mb-2">
                <div class="bg-white p-2 rounded-lg shadow-xs border border-gray-300 inline-block">
                    @if (isset($qrCode))
                        {!! $qrCode !!}
                    @else
                        <svg width="60" height="60" viewBox="0 0 60 60" class="border">
                            <rect width="60" height="60" fill="white" />
                            <g fill="black">
                                <rect x="8" y="8" width="12" height="12" />
                                <rect x="40" y="8" width="12" height="12" />
                                <rect x="8" y="40" width="12" height="12" />
                                <rect x="26" y="26" width="8" height="8" />
                            </g>
                            <text x="30" y="35" text-anchor="middle" font-size="5" fill="black">QR</text>
                        </svg>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-3 pt-2 border-t border-gray-200 text-center text-gray-500 text-xs">
            <p>Thank you for your timely payment!</p>
            <p class="mt-0.5">This receipt was generated electronically and is valid without a physical signature.</p>
            <p class="mt-0.5 font-semibold">Generated via HomeBaze Property Management System | <strong>Powered by
                    DevCentric</strong></p>
        </div>
    </div>
</body>

</html>
