<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Receipt {{ $receipt->receipt_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {!! isset($customCss) ? $customCss : '' !!}
    <style>
        @page {
            margin: 0.3cm;
            size: A4 landscape;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-size: 10pt;
        }

        .pattern-diagonal-lines {
            background-image: repeating-linear-gradient(45deg, currentColor 0, currentColor 1px, transparent 0, transparent 50%);
            background-size: 10px 10px;
        }

        .checkbox {
            position: relative;
            width: 14px;
            height: 14px;
            border: 1px solid #6b7280;
            border-radius: 2px;
            margin-right: 6px;
            background-color: white;
            display: inline-block;
        }

        .checkbox.checked {
            background-color: #059669;
            border-color: #059669;
        }

        .checkbox.checked:after {
            content: '✓';
            position: absolute;
            top: -1px;
            left: 1px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            line-height: 14px;
        }

        p {
            margin-top: 0;
            margin-bottom: 0.2em;
        }

        .wide-layout {
            max-width: 1100px;
            margin: 0 auto;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .text-sm {
            font-size: 0.8rem;
        }

        .text-base {
            font-size: 0.9rem;
        }

        .text-lg {
            font-size: 1rem;
        }

        .text-xl {
            font-size: 1.1rem;
        }

        .text-2xl {
            font-size: 1.3rem;
        }

        .text-3xl {
            font-size: 1.5rem;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .gap-6 {
            gap: 1.5rem;
        }

        .gap-8 {
            gap: 2rem;
        }

        .p-2 {
            padding: 0.4rem;
        }

        .p-3 {
            padding: 0.6rem;
        }

        .p-4 {
            padding: 0.8rem;
        }

        .p-6 {
            padding: 1rem;
        }

        .p-8 {
            padding: 1.2rem;
        }

        .px-3 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.75rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .pt-4 {
            padding-top: 1rem;
        }

        .space-y-2 > * + * {
            margin-top: 0.5rem;
        }

        .space-y-4 > * + * {
            margin-top: 1rem;
        }

        .space-y-6 > * + * {
            margin-top: 1.5rem;
        }

        .grid {
            display: grid;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .items-end {
            align-items: flex-end;
        }

        .justify-between {
            justify-content: space-between;
        }

        .space-x-2 > * + * {
            margin-left: 0.5rem;
        }

        .space-x-3 > * + * {
            margin-left: 0.75rem;
        }

        .space-x-4 > * + * {
            margin-left: 1rem;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .border {
            border-width: 0.5px;
        }

        .border-2 {
            border-width: 1px;
        }

        .border-l-4 {
            border-left-width: 2px;
        }

        .border-b-2 {
            border-bottom-width: 1px;
        }

        .border-t {
            border-top-width: 0.5px;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .relative {
            position: relative;
        }

        .absolute {
            position: absolute;
        }

        .inset-0 {
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .right-10 {
            right: 2.5rem;
        }

        .bottom-10 {
            bottom: 2.5rem;
        }

        .z-10 {
            z-index: 10;
        }

        .transform {
            transform: translateZ(0);
        }

        .rotate-12 {
            transform: rotate(12deg);
        }

        .opacity-5 {
            opacity: 0.05;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }

        .italic {
            font-style: italic;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .text-gray-700 {
            color: #374151;
        }

        .text-gray-800 {
            color: #1f2937;
        }

        .text-indigo-700 {
            color: #3730a3;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .text-green-600 {
            color: #16a34a;
        }

        .text-yellow-600 {
            color: #ca8a04;
        }

        .bg-white {
            background-color: #ffffff;
        }

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .bg-green-50 {
            background-color: #f0fdf4;
        }

        .bg-red-50 {
            background-color: #fef2f2;
        }

        .bg-yellow-50 {
            background-color: #fefce8;
        }

        .bg-indigo-50 {
            background-color: #eef2ff;
        }

        .bg-indigo-100 {
            background-color: #e0e7ff;
        }

        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-stops));
        }

        .from-slate-50 {
            --tw-gradient-from: #f8fafc;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(248, 250, 252, 0));
        }

        .to-slate-100 {
            --tw-gradient-to: #f1f5f9;
        }

        .from-indigo-50 {
            --tw-gradient-from: #eef2ff;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(238, 242, 255, 0));
        }

        .to-blue-50 {
            --tw-gradient-to: #eff6ff;
        }

        .border-gray-200 {
            border-color: #e5e7eb;
        }

        .border-gray-300 {
            border-color: #d1d5db;
        }

        .border-gray-400 {
            border-color: #9ca3af;
        }

        .border-blue-500 {
            border-color: #3b82f6;
        }

        .border-green-500 {
            border-color: #22c55e;
        }

        .border-red-500 {
            border-color: #ef4444;
        }

        .border-indigo-100 {
            border-color: #e0e7ff;
        }

        .border-indigo-200 {
            border-color: #c7d2fe;
        }

        .border-indigo-500 {
            border-color: #6366f1;
        }

        .w-16 {
            width: 4rem;
        }

        .w-32 {
            width: 8rem;
        }

        .w-48 {
            width: 12rem;
        }

        .w-56 {
            width: 14rem;
        }

        .h-56 {
            height: 14rem;
        }

        .max-w-7xl {
            max-width: 80rem;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .drop-shadow-md {
            filter: drop-shadow(0 4px 3px rgba(0, 0, 0, 0.07));
        }
    </style>
</head>

<body class="bg-gray-50">
    <div id="capture-area" class="wide-layout p-4 bg-gradient-to-r from-slate-50 to-slate-100 shadow-lg rounded border border-gray-200 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-5 pattern-diagonal-lines pattern-gray-700"></div>

        <!-- Watermark/seal effect -->
        <div class="absolute right-10 bottom-10 opacity-5 transform rotate-12">
            <svg class="w-56 h-56" viewBox="0 0 100 100">
                <!-- Complex background pattern -->
                <defs>
                    <pattern id="microtext" patternUnits="userSpaceOnUse" width="100" height="100">
                        <text x="0" y="2" font-size="1.5" fill="currentColor" opacity="0.7">HOMEBAZE PROPERTY MANAGEMENT HOMEBAZE PROPERTY MANAGEMENT</text>
                        <text x="0" y="4" font-size="1.5" fill="currentColor" opacity="0.7">SECURE RECEIPT {{ $receipt->receipt_number }} SECURE RECEIPT</text>
                        <text x="0" y="6" font-size="1.5" fill="currentColor" opacity="0.7">HOMEBAZE PROPERTY MANAGEMENT HOMEBAZE PROPERTY MANAGEMENT</text>
                        <text x="0" y="8" font-size="1.5" fill="currentColor" opacity="0.7">SECURE RECEIPT {{ $receipt->receipt_number }} SECURE RECEIPT</text>
                    </pattern>
                </defs>

                <!-- Microtext background circle -->
                <circle cx="50" cy="50" r="42" fill="url(#microtext)" opacity="0.4"></circle>

                <!-- Outer circle with intricate pattern -->
                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="1"></circle>
                <circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-width="0.2"></circle>
                <circle cx="50" cy="50" r="43" fill="none" stroke="currentColor" stroke-width="0.1" stroke-dasharray="1,2"></circle>

                <!-- Middle decorative elements -->
                <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="0.5"></circle>
                <circle cx="50" cy="50" r="36" fill="none" stroke="currentColor" stroke-width="0.3" stroke-dasharray="3,2"></circle>
                <circle cx="50" cy="50" r="34" fill="none" stroke="currentColor" stroke-width="0.2" stroke-dasharray="1,1"></circle>

                <!-- Unique pattern elements - waves -->
                <path d="M30,50 Q40,45 50,50 Q60,55 70,50" fill="none" stroke="currentColor" stroke-width="0.2"></path>
                <path d="M30,52 Q40,57 50,52 Q60,47 70,52" fill="none" stroke="currentColor" stroke-width="0.2"></path>

                <!-- Geometric security elements -->
                <polygon points="50,28 52,30 50,32 48,30" fill="currentColor" opacity="0.7"></polygon>
                <polygon points="50,68 52,70 50,72 48,70" fill="currentColor" opacity="0.7"></polygon>
                <polygon points="28,50 30,52 28,54 26,52" fill="currentColor" opacity="0.7"></polygon>
                <polygon points="72,50 74,52 72,54 70,52" fill="currentColor" opacity="0.7"></polygon>

                <!-- Company name with subtle shadow effect -->
                <text x="50" y="42" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="currentColor">HOMEBAZE</text>
                <text x="50" y="42.3" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="currentColor" opacity="0.3">HOMEBAZE</text>
                <text x="50" y="52" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif" font-size="6" font-weight="bold" fill="currentColor">PROPERTY</text>
                <text x="50" y="52.3" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif" font-size="6" font-weight="bold" fill="currentColor" opacity="0.3">PROPERTY</text>
                <text x="50" y="58" text-anchor="middle" dominant-baseline="middle" font-family="Arial, sans-serif" font-size="6" font-weight="bold" fill="currentColor">MANAGEMENT</text>

                <!-- Radial lines with varied lengths and thicknesses -->
                <g stroke="currentColor">
                    <line x1="50" y1="10" x2="50" y2="22" stroke-width="0.3"></line>
                    <line x1="50" y1="78" x2="50" y2="90" stroke-width="0.3"></line>
                    <line x1="10" y1="50" x2="22" y2="50" stroke-width="0.3"></line>
                    <line x1="78" y1="50" x2="90" y2="50" stroke-width="0.3"></line>

                    <!-- Diagonal lines with varied lengths -->
                    <line x1="25" y1="25" x2="34" y2="34" stroke-width="0.2"></line>
                    <line x1="75" y1="25" x2="66" y2="34" stroke-width="0.2"></line>
                    <line x1="25" y1="75" x2="34" y2="66" stroke-width="0.2"></line>
                    <line x1="75" y1="75" x2="66" y2="66" stroke-width="0.2"></line>

                    <!-- Additional diagonal security lines -->
                    <line x1="36" y1="25" x2="40" y2="29" stroke-width="0.1"></line>
                    <line x1="64" y1="25" x2="60" y2="29" stroke-width="0.1"></line>
                    <line x1="36" y1="75" x2="40" y2="71" stroke-width="0.1"></line>
                    <line x1="64" y1="75" x2="60" y2="71" stroke-width="0.1"></line>
                </g>

                <!-- Unique Receipt number in small print -->
                <text x="50" y="66" text-anchor="middle" font-family="monospace" font-size="2.5" font-weight="bold">{{ $receipt->receipt_number }}</text>

                <!-- Text curved around bottom semicircle -->
                <path id="curve" d="M20,70 A30,30 0 0,0 80,70" fill="none" stroke="none"></path>
                <text font-family="Arial, sans-serif" font-size="4" font-weight="bold">
                    <textPath href="#curve" startOffset="50%" text-anchor="middle">OFFICIAL RENT RECEIPT</textPath>
                </text>

                <!-- Establishment year -->
                <text x="50" y="78" text-anchor="middle" font-family="Arial, sans-serif" font-size="3.5" font-weight="bold">EST. 2024</text>
            </svg>
        </div>

        <!-- Header Row - Horizontal Layout -->
        <div class="grid grid-cols-3 gap-4 items-center mb-3 relative z-10">
            <!-- Company Info -->
            <div class="flex items-center space-x-3">
                @php
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
                            'tagline' => 'Real Estate Agency'
                        ];
                        $businessLogo = $agency->logo ?? null;
                        $businessInitials = strtoupper(substr($agency->name, 0, 2));
                    }
                    // 2. Check for Property Owner Company
                    elseif ($receipt->lease && $receipt->lease->property && $receipt->lease->property->owner &&
                            $receipt->lease->property->owner->type === 'company' &&
                            $receipt->lease->property->owner->company_name) {
                        $owner = $receipt->lease->property->owner;
                        $businessInfo = [
                            'name' => $owner->company_name,
                            'email' => $owner->email ?? $receipt->landlord->email ?? 'support@homebaze.com',
                            'phone' => $owner->phone ?? $receipt->landlord->phone ?? '+234 (0) 123-456-7890',
                            'website' => 'www.homebaze.com', // Default for now
                            'tagline' => 'Property Management Company'
                        ];
                        $businessInitials = strtoupper(substr($owner->company_name, 0, 2));
                    }
                    // 3. Check for Independent Agent's business (if property has independent agent)
                    elseif ($receipt->lease && $receipt->lease->property && $receipt->lease->property->agent_id &&
                            !$receipt->lease->property->agency_id) {
                        // Independent agent - for now use agent's name as business name
                        // This can be enhanced when agent business info is added
                        $agentUser = \App\Models\User::find($receipt->lease->property->agent_id);
                        if ($agentUser) {
                            $businessInfo = [
                                'name' => $agentUser->name . ' Real Estate',
                                'email' => $agentUser->email,
                                'phone' => $agentUser->phone ?? '+234 (0) 123-456-7890',
                                'website' => 'www.homebaze.com',
                                'tagline' => 'Independent Real Estate Agent'
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
                            'tagline' => 'Property Management System'
                        ];
                    }
                @endphp

                @if ($businessLogo)
                    <img src="{{ $businessLogo }}" alt="Company Logo" class="w-16 drop-shadow-md">
                @else
                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center">
                        <span class="text-base font-bold text-indigo-600">{{ $businessInitials }}</span>
                    </div>
                @endif
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ $businessInfo['name'] }}</p>
                    <p class="text-xs text-gray-600">{{ $businessInfo['tagline'] }}</p>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="text-center text-gray-600 text-xs">
                <p class="font-semibold">{{ $businessInfo['email'] }}</p>
                <p>{{ $businessInfo['website'] }}</p>
                <p class="text-xs">{{ $businessInfo['phone'] }}</p>
            </div>

            <!-- Receipt Number -->
            <div class="text-right">
                <div class="bg-indigo-100 px-2 py-1 rounded shadow-sm border border-indigo-200">
                    <p class="text-xs text-gray-600">Receipt No:</p>
                    <p class="text-sm font-bold text-indigo-700">{{ $receipt->receipt_number }}</p>
                </div>
            </div>
        </div>

        <!-- Main Content Row - Split into Left and Right -->
        <div class="grid grid-cols-2 gap-4 mb-3">
            <!-- Left Column -->
            <div class="space-y-4">
                <!-- Tenant & Date Info -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Received From:</p>
                        <p class="text-sm text-gray-800">{{ $receipt->tenant->name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                        <p class="text-xs font-semibold text-gray-600 mb-1">Payment Date:</p>
                        <p class="text-sm text-gray-800">{{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('F j, Y') : now()->format('F j, Y') }}</p>
                    </div>
                </div>

                <!-- Property Information -->
                @if($receipt->lease && $receipt->lease->property)
                <div class="bg-blue-50 p-3 rounded-lg border-l-4 border-blue-500 shadow-sm">
                    <p class="font-semibold text-gray-700 mb-1 text-xs">Property Details</p>
                    <p class="text-gray-800 font-medium text-sm">{{ $receipt->lease->property->title }}</p>
                    @if($receipt->lease->property->address)
                        <p class="text-xs text-gray-600 mt-1">{{ $receipt->lease->property->address }}</p>
                    @endif
                </div>
                @endif

                <!-- Lease Information -->
                @if($receipt->lease)
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-green-50 p-3 rounded-lg border-l-4 border-green-500 shadow-sm">
                        <p class="font-semibold text-gray-700 text-xs">Lease Start:</p>
                        <p class="text-gray-800 text-sm">{{ $receipt->lease->start_date ? \Carbon\Carbon::parse($receipt->lease->start_date)->format('F j, Y') : 'N/A' }}</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg border-l-4 border-red-500 shadow-sm">
                        <p class="font-semibold text-gray-700 text-xs">Lease End:</p>
                        <p class="text-gray-800 text-sm">{{ $receipt->lease->end_date ? \Carbon\Carbon::parse($receipt->lease->end_date)->format('F j, Y') : 'N/A' }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <!-- Amount Section -->
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-4 rounded-lg shadow-lg border-2 border-indigo-100">
                    <p class="text-sm font-semibold text-gray-700 mb-1">Total Amount</p>
                    <p class="text-2xl font-bold text-indigo-700">₦{{ number_format($receipt->amount, 2) }}</p>
                    <div class="mt-3 p-2 bg-white rounded text-xs">
                        <p class="font-semibold text-gray-600">Amount in Words:</p>
                        <p class="text-gray-800 italic">{{ $amountInWords }}</p>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                    <p class="font-semibold text-gray-700 mb-2 text-xs">Payment Information</p>
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment For:</span>
                            <span class="font-medium">{{ $receipt->payment_period ?? 'Rent Payment' }}</span>
                        </div>
                        @if($receipt->notes)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Notes:</span>
                            <span class="font-medium">{{ $receipt->notes }}</span>
                        </div>
                        @endif
                        @if($receipt->late_fee > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Late Fee:</span>
                            <span class="font-medium">₦{{ number_format($receipt->late_fee, 2) }}</span>
                        </div>
                        @endif
                        @if($receipt->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount:</span>
                            <span class="font-medium">-₦{{ number_format($receipt->discount, 2) }}</span>
                        </div>
                        @endif
                        @if($receipt->deposit > 0)
                        <div class="flex justify-between text-blue-600">
                            <span>Deposit:</span>
                            <span class="font-medium">₦{{ number_format($receipt->deposit, 2) }}</span>
                        </div>
                        @endif
                        @if($receipt->balance_due > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Balance Due:</span>
                            <span class="font-medium">₦{{ number_format($receipt->balance_due, 2) }}</span>
                        </div>
                        @endif
                        @if($receipt->lease && $receipt->lease->security_deposit)
                        <div class="flex justify-between text-yellow-600">
                            <span>Security Deposit:</span>
                            <span class="font-medium">₦{{ number_format($receipt->lease->security_deposit, 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row - Payment Methods & QR Code -->
        <div class="grid grid-cols-2 gap-4 items-end">
            <!-- Payment Methods -->
            <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                <p class="font-semibold text-gray-700 mb-2 text-xs">Payment Method</p>
                <div class="flex gap-1">
                    <div class="flex items-center space-x-1 px-1 py-1 rounded border text-xs {{ $receipt->payment_method == 'cash' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                        <div class="checkbox @if($receipt->payment_method == 'cash') checked @endif"></div>
                        <span>Cash</span>
                    </div>
                    <div class="flex items-center space-x-1 px-1 py-1 rounded border text-xs {{ $receipt->payment_method == 'transfer' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                        <div class="checkbox @if($receipt->payment_method == 'transfer') checked @endif"></div>
                        <span>Transfer</span>
                    </div>
                    <div class="flex items-center space-x-1 px-1 py-1 rounded border text-xs {{ $receipt->payment_method == 'pos' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                        <div class="checkbox @if($receipt->payment_method == 'pos') checked @endif"></div>
                        <span>POS</span>
                    </div>
                    <div class="flex items-center space-x-1 px-1 py-1 rounded border text-xs {{ $receipt->payment_method == 'card' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                        <div class="checkbox @if($receipt->payment_method == 'card') checked @endif"></div>
                        <span>Card</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="flex justify-end">
                <div class="text-center">
                    <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-300 mb-2 inline-block">
                        @if(isset($qrCode))
                            {!! $qrCode !!}
                        @else
                            <svg width="80" height="80" viewBox="0 0 80 80" class="border">
                                <rect width="80" height="80" fill="white"/>
                                <g fill="black">
                                    <rect x="5" y="5" width="15" height="15"/>
                                    <rect x="60" y="5" width="15" height="15"/>
                                    <rect x="5" y="60" width="15" height="15"/>
                                    <rect x="35" y="35" width="10" height="10"/>
                                </g>
                                <text x="40" y="45" text-anchor="middle" font-size="6" fill="black">QR</text>
                            </svg>
                        @endif
                    </div>
                    <p class="text-xs text-gray-600 font-medium">Scan to verify receipt</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-3 pt-2 border-t border-gray-200 text-center text-gray-500 text-xs">
            <p>Thank you for your timely payment!</p>
            <p class="mt-1">This receipt was generated electronically and is valid without a physical signature.</p>
            <p class="mt-1 font-semibold">Generated via HomeBaze Property Management System | <strong>Powered by DevCentric</strong></p>
        </div>
    </div>
</body>
</html>