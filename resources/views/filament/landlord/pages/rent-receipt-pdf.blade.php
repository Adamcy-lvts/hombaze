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
        /* print optimizations */
        @media print {
            body { -webkit-print-color-adjust: exact; }
        }
        .text-xxs { font-size: 0.65rem; line-height: 1rem; }
    </style>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div id="capture-area"
        class="bg-linear-to-r from-slate-50 to-slate-100 p-6 shadow-2xl rounded-xl border border-gray-200 w-full max-w-3xl relative overflow-hidden">
        
        <!-- Premium subtle background pattern -->
        <div class="absolute inset-0 opacity-5 pattern-diagonal-lines pattern-gray-700 pattern-size-2 pattern-bg-transparent pointer-events-none"></div>

        <!-- Header: Logo & Receipt # -->
        <div class="flex justify-between items-start mb-6 relative z-10 border-b border-gray-200 pb-4">
            <!-- Left: Business Info -->
            <div class="flex items-center space-x-3">
                 @php
                    $inlineLogo = function (string $relativePath): ?string {
                        $fullPath = public_path($relativePath);
                        if (!is_file($fullPath)) return null;
                        $contents = file_get_contents($fullPath);
                        if ($contents === false) return null;
                        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                        $mime = $extension === 'svg' ? 'image/svg+xml' : "image/{$extension}";
                        return 'data:' . $mime . ';base64,' . base64_encode($contents);
                    };
                    $inlineStorageLogo = function (?string $storagePath): ?string {
                        if (!$storagePath) return null;
                        $fullPath = storage_path('app/public/' . ltrim($storagePath, '/'));
                        if (!is_file($fullPath)) return null;
                        $contents = file_get_contents($fullPath);
                        if ($contents === false) return null;
                        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                        $mime = $extension === 'svg' ? 'image/svg+xml' : "image/{$extension}";
                        return 'data:' . $mime . ';base64,' . base64_encode($contents);
                    };

                    $businessInfo = null;
                    $businessLogo = null;
                    $businessInitials = 'HB';

                    // Business detection logic
                    if ($receipt->lease && $receipt->lease->property && $receipt->lease->property->agency) {
                        $agency = $receipt->lease->property->agency;
                        $businessInfo = [
                            'name' => $agency->name,
                            'email' => $agency->email ?? 'support@homebaze.live',
                            'phone' => $agency->phone ?? '+2347071940611',
                            'website' => $agency->website,
                        ];
                        $businessLogo = $inlineStorageLogo($agency->logo) ?? $inlineLogo('images/app-logo.svg');
                        $businessInitials = strtoupper(substr($agency->name, 0, 2));
                    } elseif ($receipt->lease && $receipt->lease->property && $receipt->lease->property->owner && $receipt->lease->property->owner->type === 'company' && $receipt->lease->property->owner->company_name) {
                        $owner = $receipt->lease->property->owner;
                        $businessInfo = [
                            'name' => $owner->company_name,
                            'email' => $owner->email ?? ($receipt->landlord->email ?? 'support@homebaze.live'),
                            'phone' => $owner->phone ?? ($receipt->landlord->phone ?? '+2347071940611'),
                            'website' => $owner->website,
                        ];
                        $businessInitials = strtoupper(substr($owner->company_name, 0, 2));
                    } elseif ($receipt->lease && $receipt->lease->property && $receipt->lease->property->agent_id && !$receipt->lease->property->agency_id) {
                         $agentUser = \App\Models\User::find($receipt->lease->property->agent_id);
                         if ($agentUser) {
                            $businessInfo = [
                                'name' => $agentUser->name . ' Real Estate',
                                'email' => $agentUser->email,
                                'phone' => $agentUser->phone ?? '+2347071940611',
                            ];
                            $businessInitials = strtoupper(substr($agentUser->name, 0, 2));
                         }
                    }

                    if (!$businessInfo) {
                        $businessInfo = [
                            'name' => 'HomeBaze Property',
                            'email' => 'support@homebaze.live',
                            'phone' => '+2347071940611',
                        ];
                        $businessLogo = $inlineLogo('images/app-logo.svg');
                    }
                @endphp

                @if ($businessLogo)
                    <img src="{{ $businessLogo }}" alt="Logo" class="w-16 h-16 object-contain drop-shadow-sm">
                @else
                    <div class="w-16 h-16 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-200">
                        <span class="text-lg font-bold text-white">{{ $businessInitials }}</span>
                    </div>
                @endif
                <div>
                     <h2 class="text-sm font-bold text-gray-900 leading-tight">{{ $businessInfo['name'] }}</h2>
                     <div class="text-xs text-gray-500 mt-0.5 space-y-0.5">
                        <p>{{ $businessInfo['email'] }}</p>
                        <p>{{ $businessInfo['phone'] }}</p>
                     </div>
                </div>
            </div>

            <!-- Right: Receipt Details -->
            <div class="text-right">
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Receipt No.</p>
                <p class="text-sm font-bold text-gray-900 font-mono">{{ $receipt->receipt_number }}</p>
                <div class="mt-2 text-xs text-gray-500">
                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 font-medium">
                        {{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('M d, Y') : now()->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Content Grid: 2 Columns -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            
            <!-- Left Column: Parties (Col-5) -->
            <div class="col-span-5 space-y-5">
                <!-- Received From -->
                <div>
                    <h3 class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-2">Received From</h3>
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                        <p class="text-sm font-bold text-gray-900">{{ $receipt->tenant_name ?? 'N/A' }}</p>
                        <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                            <p>{{ $receipt->tenant_email ?? '' }}</p>
                            <p>{{ $receipt->tenant_phone ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Received By (Landlord) -->
                <div>
                    <h3 class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-2">Received By</h3>
                    <div class="pl-3 border-l-2 border-indigo-100">
                         <p class="text-sm font-semibold text-gray-800">{{ $receipt->landlord->name ?? 'Landlord' }}</p>
                         <p class="text-xs text-gray-500">{{ $receipt->landlord->email ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & Payment (Col-7) -->
            <div class="col-span-7 flex flex-col h-full relative z-10">
                <div class="flex-1">
                    <h3 class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-2">Property & Payment Details</h3>
                    
                    <div class="space-y-3">
                        <!-- Property (Blue Accent) -->
                        @if ($receipt->property_title)
                        <div class="bg-blue-50/50 p-2.5 rounded-lg border-l-4 border-blue-500 shadow-xs">
                             <div class="flex items-start text-xs">
                                 <div class="w-4 h-4 mr-2 mt-0.5 text-blue-400">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                 </div>
                                 <div class="flex-1">
                                     <p class="font-semibold text-gray-800">{{ $receipt->property_title }}</p>
                                     <p class="text-gray-500 text-xs mt-0.5">{{ $receipt->property_address ?? '' }}</p>
                                 </div>
                             </div>
                        </div>
                        @endif

                        <!-- Payment For -->
                        <div class="flex items-center text-xs p-1">
                             <div class="w-4 h-4 mr-2 text-gray-400">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                             </div>
                             <p class="text-gray-700"><span class="font-medium text-gray-900">Payment For:</span> {{ $receipt->payment_for ?? $receipt->payment_period ?? 'Rent Payment' }}</p>
                        </div>
                        
                        <!-- Dates (Green/Red Accents) - Show lease dates OR custom dates -->
                        @php
                            $startDate = null;
                            $endDate = null;
                            $dateLabel = 'Period';
                            
                            if ($receipt->lease) {
                                $startDate = $receipt->lease->start_date;
                                $endDate = $receipt->lease->end_date;
                                $dateLabel = 'Lease';
                            } elseif ($receipt->custom_start_date || $receipt->custom_end_date) {
                                $startDate = $receipt->custom_start_date;
                                $endDate = $receipt->custom_end_date;
                                $dateLabel = 'Period';
                            }
                        @endphp
                        
                        @if ($startDate || $endDate)
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            @if ($startDate)
                            <div class="bg-green-50 p-2 rounded border-l-2 border-green-500">
                                <p class="text-[10px] text-green-700 font-semibold uppercase">{{ $dateLabel }} Start</p>
                                <p class="font-medium text-gray-700">
                                    {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} 
                                </p>
                            </div>
                            @endif
                            @if ($endDate)
                            <div class="bg-red-50 p-2 rounded border-l-2 border-red-500">
                                <p class="text-[10px] text-red-700 font-semibold uppercase">{{ $dateLabel }} End</p>
                                <p class="font-medium text-gray-700">
                                    {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                                </p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Financials Compact -->
                <div class="bg-gray-50/80 rounded-lg p-4 mt-4 border border-gray-200">
                    <div class="space-y-2 text-xs">
                         <div class="flex justify-between items-center text-gray-600">
                             <span>Base Amount</span>
                             <span>₦{{ number_format($receipt->amount, 2) }}</span>
                         </div>
                         
                         @if(($receipt->late_fee ?? 0) > 0)
                         <div class="flex justify-between items-center text-red-600 bg-red-50 p-1.5 rounded">
                             <span>Late Fee</span>
                             <span>+₦{{ number_format($receipt->late_fee, 2) }}</span>
                         </div>
                         @endif
                         
                         @if(($receipt->discount ?? 0) > 0)
                         <div class="flex justify-between items-center text-green-700 bg-green-50 p-1.5 rounded">
                             <span>Discount</span>
                             <span>-₦{{ number_format($receipt->discount, 2) }}</span>
                         </div>
                         @endif
                         
                         @if(($receipt->deposit ?? 0) > 0)
                         <div class="flex justify-between items-center text-blue-700 bg-blue-50 p-1.5 rounded">
                             <span>Deposit Paid</span>
                             <span>-₦{{ number_format($receipt->deposit, 2) }}</span>
                         </div>
                         @endif
                    </div>

                    <div class="border-t border-gray-300 my-2 pt-2 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-700">TOTAL PAID</span>
                        <span class="text-lg font-bold text-indigo-700">₦{{ number_format($receipt->amount, 2) }}</span>
                    </div>
                     
                    @if(($receipt->balance_due ?? 0) > 0)
                    <div class="flex justify-end pt-1">
                        <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 px-3 py-1 rounded-full shadow-sm">
                            Balance Due: ₦{{ number_format($receipt->balance_due, 2) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer Row -->
        <div class="flex items-end justify-between mt-auto pt-4 border-t border-gray-100">
             <div class="w-2/3">
                 <p class="text-xs font-semibold text-gray-400 mb-1">Amount in words</p>
                 <p class="text-sm italic text-gray-600 font-medium font-serif leading-snug">"{{ $amountInWords }}"</p>
                 
                 <div class="flex items-center space-x-4 mt-3">
                     <div class="flex items-center space-x-1.5 px-2 py-1 bg-gray-50 rounded border border-gray-100">
                         <div class="w-1.5 h-1.5 rounded-full {{ $receipt->status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                         <span class="text-xs font-medium text-gray-700 capitalize">{{ $receipt->status }}</span>
                     </div>
                     <div class="flex items-center space-x-1.5">
                         <span class="text-xs text-gray-400">Paid via</span>
                         <span class="text-xs font-medium text-gray-700 capitalize">{{ $receipt->payment_method }}</span>
                     </div>
                 </div>
             </div>

             <!-- QR Code -->
             <div>
                <div class="bg-white p-1 rounded shadow-sm border border-gray-100">
                     @if (isset($qrCode))
                        {!! $qrCode !!}
                    @else
                        <!-- Placeholder if QR missing in preview -->
                        <div class="w-12 h-12 bg-gray-100 flex items-center justify-center text-[8px]">QR</div>
                    @endif
                </div>
             </div>
        </div>
        
        <p class="text-[10px] text-center text-gray-300 mt-4 font-mono">
            Generated by HomeBaze • Valid without signature
        </p>
    </div>
</body>
</html>
