<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }

        .text-xs {
            font-size: 10px;
        }

        .text-sm {
            font-size: 11px;
        }

        .text-base {
            font-size: 11px;
        }

        .text-lg {
            font-size: 12px;
        }

        .text-xl {
            font-size: 14px;
        }

        .text-2xl {
            font-size: 16px;
        }

        h1,
        h2,
        h3 {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        }

        .section-accent::before {
            content: '';
            width: 3px;
            height: 12px;
            background: #059669;
            margin-right: 6px;
            display: inline-block;
        }

        .dot-accent::before {
            content: '';
            width: 4px;
            height: 4px;
            background: #059669;
            border-radius: 50%;
            margin-right: 4px;
            display: inline-block;
        }

        .amount-highlight {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }

        .receipt-box {
            border: 2px solid #059669;
            border-radius: 8px;
            padding: 12px;
            background: #f0fdf4;
        }
    </style>
</head>

<body class="text-gray-800 bg-white">
    <!-- Document Container - Edge to Edge -->
    <div class="bg-white border border-gray-300 overflow-hidden">

        <!-- Header Section -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-4 text-center">
            <h1 class="text-xl font-bold mb-2 tracking-wide">PAYMENT RECEIPT</h1>
            <div class="w-16 h-0.5 bg-white bg-opacity-60 mx-auto mb-1"></div>
            <p class="text-sm font-medium">{{ $record->receipt_number }}</p>
        </div>

        <!-- Main Content -->
        <div class="p-4 space-y-4">

            <!-- Receipt Information -->
            <section>
                <div class="receipt-box">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-green-700 mb-2">Receipt Details</h3>
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Receipt No:</span>
                                    <span class="text-xs text-gray-800 font-mono">{{ $record->receipt_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Date Issued:</span>
                                    <span class="text-xs text-gray-800">{{ now()->format('M j, Y') }}</span>
                                </div>
                                @if($record->payment_date)
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Payment Date:</span>
                                    <span class="text-xs text-gray-800">{{ $record->payment_date->format('M j, Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-green-700 mb-2">Status</h3>
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Payment Status:</span>
                                    <span class="text-xs px-2 py-0.5 rounded font-medium
                                        @if($record->status === 'paid') bg-green-100 text-green-800
                                        @elseif($record->status === 'partial') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Method:</span>
                                    <span class="text-xs text-gray-800">{{ $record->getPaymentMethods()[$record->payment_method] ?? 'N/A' }}</span>
                                </div>
                                @if($record->payment_reference)
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Reference:</span>
                                    <span class="text-xs text-gray-800 font-mono">{{ $record->payment_reference }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Parties Section -->
            <section>
                <h2 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1 flex items-center section-accent">
                    PAYMENT PARTIES
                </h2>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Landlord -->
                    <div class="bg-gray-50 p-3 rounded border border-gray-200">
                        <h3 class="text-xs font-bold text-gray-700 mb-2 flex items-center dot-accent">
                            RECEIVED BY (LANDLORD)
                        </h3>
                        <p class="text-sm font-semibold text-gray-800 mb-1">{{ $record->landlord->name ?? 'Landlord Name' }}</p>
                        <p class="text-xs text-gray-600">{{ $record->landlord->email ?? 'landlord@example.com' }}</p>
                    </div>

                    <!-- Tenant -->
                    <div class="bg-gray-50 p-3 rounded border border-gray-200">
                        <h3 class="text-xs font-bold text-gray-700 mb-2 flex items-center dot-accent">
                            PAID BY (TENANT)
                        </h3>
                        <p class="text-sm font-semibold text-gray-800 mb-1">{{ $record->tenant->name ?? 'Tenant Name' }}</p>
                        <p class="text-xs text-gray-600">{{ $record->tenant->email ?? 'tenant@example.com' }}</p>
                    </div>
                </div>
            </section>

            <!-- Property Details -->
            <section>
                <h2 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1 flex items-center section-accent">
                    PROPERTY INFORMATION
                </h2>

                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <div class="flex items-start">
                                <span class="text-xs font-semibold text-gray-600 w-16 flex-shrink-0">Property:</span>
                                <span class="text-xs text-gray-800 font-medium">{{ $record->lease->property->title ?? 'Property Title' }}</span>
                            </div>
                            @if($record->lease->property->address)
                            <div class="flex items-start">
                                <span class="text-xs font-semibold text-gray-600 w-16 flex-shrink-0">Address:</span>
                                <span class="text-xs text-gray-800">{{ $record->lease->property->address }}</span>
                            </div>
                            @endif
                            <div class="flex items-start">
                                <span class="text-xs font-semibold text-gray-600 w-16 flex-shrink-0">Type:</span>
                                <span class="text-xs text-gray-800">{{ $record->lease->property->propertyType->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            @if($record->payment_for_period)
                            <div class="flex items-start">
                                <span class="text-xs font-semibold text-gray-600 w-16 flex-shrink-0">Period:</span>
                                <span class="text-xs text-gray-800 font-medium">{{ $record->payment_for_period }}</span>
                            </div>
                            @endif
                            <div class="flex items-start">
                                <span class="text-xs font-semibold text-gray-600 w-16 flex-shrink-0">Frequency:</span>
                                <span class="text-xs text-gray-800 capitalize">{{ str_replace('_', ' ', $record->lease->payment_frequency ?? 'N/A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Lease Validity & Renewal Information -->
            <section>
                <h2 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1 flex items-center section-accent">
                    LEASE VALIDITY & RENEWAL
                </h2>

                <div class="bg-blue-50 p-3 rounded border border-blue-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-blue-700 mb-2">Lease Period</h3>
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Start Date:</span>
                                    <span class="text-xs text-gray-800 font-medium">{{ $record->lease->start_date->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">End Date:</span>
                                    <span class="text-xs text-gray-800 font-medium">{{ $record->lease->end_date->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Duration:</span>
                                    <span class="text-xs text-gray-800 font-medium">{{ $record->lease->start_date->diffInMonths($record->lease->end_date) }} Months</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Status:</span>
                                    <span class="text-xs px-2 py-0.5 rounded font-medium
                                        @if($record->lease->status === 'active') bg-green-100 text-green-800
                                        @elseif($record->lease->status === 'expired') bg-red-100 text-red-800
                                        @elseif($record->lease->status === 'draft') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($record->lease->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-blue-700 mb-2">Renewal Information</h3>
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-xs font-semibold text-gray-600">Renewal Option:</span>
                                    <span class="text-xs px-2 py-0.5 rounded font-medium
                                        {{ $record->lease->renewal_option ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $record->lease->renewal_option ? 'Available' : 'Not Available' }}
                                    </span>
                                </div>
                                @if($record->lease->renewal_option)
                                <div class="mt-2 p-2 bg-green-50 rounded border border-green-200">
                                    <p class="text-xs text-green-800">
                                        <strong>Renewal Terms:</strong> This lease may be renewed upon mutual agreement between landlord and tenant before the expiration date ({{ $record->lease->end_date->format('M j, Y') }}).
                                    </p>
                                </div>
                                @else
                                <div class="mt-2 p-2 bg-red-50 rounded border border-red-200">
                                    <p class="text-xs text-red-800">
                                        <strong>No Renewal:</strong> This lease will terminate on {{ $record->lease->end_date->format('M j, Y') }} and is not eligible for renewal.
                                    </p>
                                </div>
                                @endif
                                
                                @php
                                    $daysToExpiry = $record->lease->end_date->diffInDays(now(), false);
                                    $isExpiringSoon = $daysToExpiry >= -30 && $daysToExpiry <= 0;
                                    $isExpired = $daysToExpiry > 0;
                                @endphp
                                
                                @if($isExpired)
                                <div class="mt-2 p-2 bg-red-100 rounded border border-red-300">
                                    <p class="text-xs text-red-800 font-medium">
                                        ⚠️ LEASE EXPIRED: {{ abs($daysToExpiry) }} days ago
                                    </p>
                                </div>
                                @elseif($isExpiringSoon)
                                <div class="mt-2 p-2 bg-yellow-100 rounded border border-yellow-300">
                                    <p class="text-xs text-yellow-800 font-medium">
                                        ⏰ EXPIRES SOON: {{ abs($daysToExpiry) }} days remaining
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Payment Breakdown -->
            <section>
                <h2 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1 flex items-center section-accent">
                    PAYMENT BREAKDOWN
                </h2>

                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Base Amount:</span>
                            <span class="text-sm text-gray-800 font-mono">₦{{ number_format($record->amount, 2) }}</span>
                        </div>

                        @if($record->late_fee && $record->late_fee > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Late Fee:</span>
                            <span class="text-sm text-red-600 font-mono">+₦{{ number_format($record->late_fee, 2) }}</span>
                        </div>
                        @endif

                        @if($record->discount && $record->discount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Discount:</span>
                            <span class="text-sm text-green-600 font-mono">-₦{{ number_format($record->discount, 2) }}</span>
                        </div>
                        @endif

                        <div class="border-t border-gray-300 pt-2 mt-3">
                            <div class="amount-highlight">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold">TOTAL AMOUNT:</span>
                                    <span class="text-lg font-bold font-mono">₦{{ number_format($record->net_amount ?? $record->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if($record->notes)
            <!-- Notes Section -->
            <section>
                <h2 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1 flex items-center section-accent">
                    PAYMENT NOTES
                </h2>

                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                    <p class="text-xs text-gray-800 leading-tight">{{ $record->notes }}</p>
                </div>
            </section>
            @endif

            <!-- Verification Section -->
            <section>
                <h2 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-300 pb-1 flex items-center section-accent">
                    RECEIPT VERIFICATION
                </h2>

                <div class="bg-green-50 p-3 rounded border border-green-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-700 mb-2">
                                <strong>This receipt confirms payment of ₦{{ number_format($record->net_amount ?? $record->total_amount, 2) }}</strong> 
                                for rental period {{ $record->payment_for_period ?? 'as specified above' }} under lease agreement dated {{ $record->lease->start_date->format('M j, Y') }}.
                            </p>
                            <p class="text-xs text-gray-600 mb-2">
                                <strong>Lease Validity:</strong> {{ $record->lease->start_date->format('M j, Y') }} to {{ $record->lease->end_date->format('M j, Y') }}
                                @if($record->lease->renewal_option) • <span class="text-green-600 font-medium">Renewable</span> @endif
                            </p>
                            <p class="text-xs text-gray-600">
                                Payment processed through HomeBaze Property Management System on {{ now()->format('F j, Y \\a\\t g:i A') }}.
                            </p>
                        </div>
                        <div class="text-center">
                            <div class="border-t border-gray-400 pt-2 mt-6">
                                <p class="text-xs font-semibold text-gray-700">Authorized Signature</p>
                                <p class="text-xs text-gray-600">{{ $record->landlord->name ?? 'Landlord Name' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ now()->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <div class="bg-gradient-to-r from-gray-100 to-gray-200 border-t border-gray-300 p-3 text-center">
            <p class="text-xs text-gray-600 font-medium mb-1">
                Receipt generated on {{ now()->format('F j, Y \\a\\t g:i A') }}
            </p>
            <p class="text-xs text-gray-500">
                via HomeBaze Property Management System | Receipt #{{ $record->receipt_number }}
            </p>
        </div>
    </div>
</body>

</html>