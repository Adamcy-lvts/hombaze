<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');
        
        body { font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .text-xxs { font-size: 0.6rem; line-height: 0.8rem; }
        
        .pattern-diagonal-lines {
            background-image: repeating-linear-gradient(45deg, #e5e7eb 0, #e5e7eb 1px, transparent 0, transparent 50%);
            background-size: 10px 10px;
        }
    </style>
</head>

<body class="bg-white text-gray-800 p-8 max-w-4xl mx-auto">
    <!-- Main Receipt Container -->
    <div class="border border-gray-200 rounded-xl p-8 relative overflow-hidden">
        
        <!-- Decorative Background -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-green-50 rounded-bl-full opacity-60 -z-10"></div>
        
        <!-- Header Section -->
        <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-6">
            <div class="flex items-center space-x-4">
                 <!-- Logo Logic -->
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

                    $businessName = 'HomeBaze Property Management'; // Default
                    $businessLogo = null;
                    $businessInitials = 'HB';

                    if ($record->lease && $record->lease->property && $record->lease->property->agency) {
                        $agency = $record->lease->property->agency;
                        $businessName = $agency->name;
                        $businessLogo = $inlineStorageLogo($agency->logo) ?? $inlineLogo('images/app-logo.svg');
                        $businessInitials = strtoupper(substr($agency->name, 0, 2));
                    } elseif ($record->lease && $record->lease->property && $record->lease->property->owner && $record->lease->property->owner->type === 'company' && $record->lease->property->owner->company_name) {
                         $owner = $record->lease->property->owner;
                         $businessName = $owner->company_name;
                         $businessInitials = strtoupper(substr($owner->company_name, 0, 2));
                    } elseif ($record->lease && $record->lease->property && $record->lease->property->agent_id && !$record->lease->property->agency_id) {
                         $agentUser = \App\Models\User::find($record->lease->property->agent_id);
                         if ($agentUser) {
                             $businessName = $agentUser->name . ' Real Estate';
                             $businessInitials = strtoupper(substr($agentUser->name, 0, 2));
                         }
                    } else {
                         // Default logic fallbacks
                         $businessLogo = $inlineLogo('images/app-logo.svg');
                    }
                 @endphp

                 @if ($businessLogo)
                    <img src="{{ $businessLogo }}" alt="Logo" class="w-16 h-16 object-contain">
                 @else
                    <div class="w-16 h-16 bg-green-600 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        {{ $businessInitials }}
                    </div>
                 @endif

                 <div>
                     <h1 class="text-xl font-bold text-gray-900 tracking-tight">PAYMENT RECEIPT</h1>
                     <p class="text-xs text-gray-500 mt-1">{{ $businessName }}</p>
                 </div>
            </div>

            <div class="text-right">
                <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">Receipt Number</p>
                <p class="text-lg font-mono font-bold text-gray-900">{{ $record->receipt_number }}</p>
                <p class="text-xs text-gray-500 mt-1">Date: {{ now()->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- 2-Column Content Grid -->
        <div class="grid grid-cols-12 gap-8 mb-8">
            
            <!-- Left Column: Parties (5 cols) -->
            <div class="col-span-5 space-y-6">
                <!-- Tenant -->
                <div>
                    <h3 class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">Paid By</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <p class="font-bold text-gray-900">{{ $record->tenant_name ?? 'Tenant Name' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $record->tenant_email ?? '' }}</p>
                        <p class="text-xs text-gray-500">{{ $record->tenant_phone ?? '' }}</p>
                    </div>
                </div>

                <!-- Landlord -->
                <div>
                    <h3 class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">Received By</h3>
                    <div class="pl-4 border-l-2 border-green-200">
                        <p class="font-semibold text-gray-900">{{ $record->landlord->name ?? 'Landlord Name' }}</p>
                        <p class="text-xs text-gray-500">{{ $record->landlord->email ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Context & Financials (7 cols) -->
            <div class="col-span-7 flex flex-col">
                <div class="mb-6">
                    <h3 class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">Payment Details</h3>
                    <div class="space-y-3 text-sm">
                        <!-- Property -->
                        <div class="flex">
                            <span class="w-24 text-gray-500 text-xs shrink-0 pt-0.5">Property</span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $record->property_title ?? 'Property Title' }}</p>
                                <p class="text-xs text-gray-500">{{ $record->property_address ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Period -->
                        @if ($record->payment_for || $record->payment_for_period || $record->lease)
                        <div class="flex items-center">
                            <span class="w-24 text-gray-500 text-xs shrink-0">For Period</span>
                            <span class="text-gray-900 font-medium">
                                {{ $record->payment_for ?? $record->payment_for_period ?? 'Rent' }}
                            </span>
                        </div>
                        @endif
                        
                        <!-- Dates -->
                         @if($record->payment_date)
                        <div class="flex items-center">
                            <span class="w-24 text-gray-500 text-xs shrink-0">Payment Date</span>
                            <span class="text-gray-900 font-medium">{{ $record->payment_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Financial Breakdown Box -->
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 mt-auto">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Amount</span>
                            <span class="font-medium font-mono">₦{{ number_format($record->amount, 2) }}</span>
                        </div>
                        
                        @if(($record->late_fee ?? 0) > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Late Fee</span>
                            <span class="font-mono">+₦{{ number_format($record->late_fee, 2) }}</span>
                        </div>
                        @endif

                        @if(($record->discount ?? 0) > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span class="font-mono">-₦{{ number_format($record->discount, 2) }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 my-3 pt-3 flex justify-between items-center">
                        <span class="font-bold text-gray-900">TOTAL</span>
                        <span class="text-xl font-bold text-green-700 font-mono">₦{{ number_format($record->amount, 2) }}</span>
                    </div>

                    @if(($record->balance_due ?? 0) > 0)
                     <div class="flex justify-end pt-1">
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded">
                            Balance Due: ₦{{ number_format($record->balance_due, 2) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="border-t border-gray-100 pt-6 flex justify-between items-end">
             <div class="max-w-md">
                 <p class="text-xs text-gray-400 uppercase font-bold mb-1">Amount in Words</p>
                 <p class="italic text-gray-600 font-serif text-sm">"{{ $amountInWords ?? 'Amount in words' }}"</p>
            </div>
            
            <div class="text-right">
                <div class="inline-flex items-center space-x-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                    <div class="w-2 h-2 rounded-full {{ $record->status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                    <span class="text-xs font-bold text-gray-700 uppercase">{{ $record->status }}</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-xs font-medium text-gray-600 uppercase">{{ $record->payment_method }}</span>
                </div>
            </div>
        </div>

        <!-- Bottom Branding -->
        <div class="mt-8 text-center">
            <p class="text-xxs text-gray-400">
                Generated electronically by HomeBaze Property Management System.<br>
                Valid as proof of payment without physical signature.
            </p>
        </div>

    </div>
</body>
</html>