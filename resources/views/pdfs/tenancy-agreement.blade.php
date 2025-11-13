<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Agreement</title>

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

        .text-md {
            font-size: 12px;
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
            background: #6b7280;
            margin-right: 6px;
            display: inline-block;
        }

        .dot-accent::before {
            content: '';
            width: 4px;
            height: 4px;
            background: #6b7280;
            border-radius: 50%;
            margin-right: 4px;
            display: inline-block;
        }

        .terms-number {
            width: 16px;
            height: 16px;
            background: #6b7280;
            color: white;
            font-size: 9px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .signature-line {
            height: 20px;
            border-bottom: 1px solid #9ca3af;
            margin-bottom: 4px;
            position: relative;
        }

        .signature-line::after {
            content: 'Signature';
            position: absolute;
            bottom: -2px;
            right: 2px;
            font-size: 8px;
            color: #6b7280;
        }

        /* Rich text styling for terms and conditions */
        .prose h3 {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            margin-top: 0;
        }

        .prose ol {
            padding-left: 16px;
            margin: 0;
        }

        .prose ol li {
            margin-bottom: 4px;
            font-size: 12px;
            line-height: 1.4;
        }

        .prose ul {
            padding-left: 16px;
            margin: 0;
        }

        .prose ul li {
            margin-bottom: 4px;
            font-size: 12px;
            line-height: 1.4;
        }

        .prose strong {
            font-weight: bold;
        }

        .prose em {
            font-style: italic;
        }

        .prose h2 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 6px;
            margin-top: 8px;
        }

        .prose p {
            margin-bottom: 4px;
            font-size: 12px;
            line-height: 1.4;
        }
    </style>
</head>

<body class="text-gray-800 bg-white">
    <!-- Document Container - Edge to Edge -->
    <div class="bg-white border border-gray-300 overflow-hidden">

        <!-- Header Section -->
        <div class="bg-linear-to-r from-gray-800 to-gray-900 text-white p-3 text-center">
            <h1 class="text-lg font-bold mb-2 tracking-wide">TENANCY AGREEMENT</h1>
            <div class="w-12 h-0.5 bg-white bg-opacity-60 mx-auto"></div>
        </div>

        <!-- Main Content -->
        <div class="p-3 space-y-3">

            <!-- Parties Section -->
            <section>
                <h2
                    class="text-xs font-bold text-gray-800 mb-1 border-b border-gray-300 pb-1 flex items-center section-accent">
                    PARTIES TO THE AGREEMENT
                </h2>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Landlord -->
                    <div class="bg-gray-50 p-2 rounded-sm border border-gray-200">
                        <h3 class="text-xs font-bold text-gray-700 mb-1 flex items-center dot-accent">
                            LANDLORD
                        </h3>
                        <p class="text-sm font-semibold text-gray-800 mb-1">
                            {{ $record->landlord->name ?? '[Landlord Name]' }}</p>
                        <p class="text-xs text-gray-600">{{ $record->landlord->email ?? '[Landlord Email]' }}</p>
                    </div>

                    <!-- Tenant -->
                    <div class="bg-gray-50 p-2 rounded-sm border border-gray-200">
                        <h3 class="text-xs font-bold text-gray-700 mb-1 flex items-center dot-accent">
                            TENANT
                        </h3>
                        <p class="text-sm font-semibold text-gray-800 mb-1">{{ $record->tenant->name ?? '[Tenant Name]' }}
                        </p>
                        <p class="text-xs text-gray-600">{{ $record->tenant->email ?? '[Tenant Email]' }}</p>
                    </div>
                </div>
            </section>

            <!-- Property Details Section -->
            <section>
                <h2
                    class="text-xs font-bold text-gray-800 mb-1 border-b border-gray-300 pb-1 flex items-center section-accent">
                    PROPERTY DETAILS
                </h2>

                <div class="bg-gray-50 p-2 rounded-sm border border-gray-200">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1">
                            <div class="flex items-start">
                                <span class="text-xs font-semibold text-gray-600 w-16 shrink-0">Property:</span>
                                <span
                                    class="text-xs text-gray-800 font-medium">{{ $record->property->title ?? '[Property Title]' }}</span>
                            </div>
                            @if ($record->property->address)
                                <div class="flex items-start">
                                    <span class="text-xs font-semibold text-gray-600 w-16 shrink-0">Address:</span>
                                    <span class="text-xs text-gray-800">{{ $record->property->address }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-start">
                                <span class="text-xs font-semibold text-gray-600 w-16 shrink-0">Type:</span>
                                <span
                                    class="text-xs text-gray-800">{{ $record->property->propertyType->name ?? 'N/A' }}</span>
                            </div>
                            @if ($record->property->propertySubtype)
                                <div class="flex items-start">
                                    <span class="text-xs font-semibold text-gray-600 w-16 shrink-0">Subtype:</span>
                                    <span
                                        class="text-xs text-gray-800">{{ $record->property->propertySubtype->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- Lease Terms Section -->
            <section>
                <h2
                    class="text-xs font-bold text-gray-800 mb-1 border-b border-gray-300 pb-1 flex items-center section-accent">
                    LEASE TERMS & FINANCIAL DETAILS
                </h2>

                <div class="bg-gray-50 p-2 rounded-sm border border-gray-200">
                    <div class="grid grid-cols-6 gap-2 text-xs">
                        <!-- Start Date -->
                        <div class="text-center">
                            <span class="text-xs font-semibold text-gray-600 block mb-1">Start Date</span>
                            <p class="text-xs font-medium text-gray-800">{{ $record->start_date->format('M j, Y') }}</p>
                        </div>

                        <!-- End Date -->
                        <div class="text-center">
                            <span class="text-xs font-semibold text-gray-600 block mb-1">End Date</span>
                            <p class="text-xs font-medium text-gray-800">{{ $record->end_date->format('M j, Y') }}</p>
                        </div>

                        <!-- Duration -->
                        <div class="text-center">
                            <span class="text-xs font-semibold text-gray-600 block mb-1">Duration</span>
                            <p class="text-xs font-bold text-gray-800">
                                {{ $record->start_date->diffInMonths($record->end_date) }} Months</p>
                        </div>

                        <!-- Rent Amount -->
                        <div class="text-center">
                            <span class="text-xs font-semibold text-gray-600 block mb-1">Rent Amount</span>
                            <p class="text-xs font-bold text-gray-800">₦{{ number_format($record->yearly_rent, 0) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ ucfirst($record->payment_frequency) }}</p>
                        </div>

                        <!-- Payment Terms -->
                        <div class="text-center">
                            <span class="text-xs font-semibold text-gray-600 block mb-1">Payment Terms</span>
                            <p class="text-xs font-medium text-gray-800">{{ ucfirst($record->payment_frequency) }}</p>
                            @if ($record->payment_frequency === 'biannually')
                                <p class="text-xs text-gray-600">(₦{{ number_format($record->yearly_rent / 2, 0) }})
                                </p>
                            @elseif($record->payment_frequency === 'quarterly')
                                <p class="text-xs text-gray-600">(₦{{ number_format($record->yearly_rent / 4, 0) }})
                                </p>
                            @endif
                        </div>

                        <!-- Status -->
                        <div class="text-center">
                            <span class="text-xs font-semibold text-gray-600 block mb-1">Status</span>
                            <span
                                class="inline-block px-1 py-0.5 text-xs font-medium rounded 
                                @if ($record->status === 'active') bg-green-100 text-green-800
                                @elseif($record->status === 'draft') bg-gray-100 text-gray-800
                                @elseif($record->status === 'expired') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($record->status) }}
                            </span>
                            @if ($record->signed_date)
                                <p class="text-xs text-gray-600 mt-0.5">{{ $record->signed_date->format('M j, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($record->renewal_option !== null)
                        <div class="mt-1 pt-1 border-t border-gray-300 text-center">
                            <span class="text-xs font-semibold text-gray-600 mr-1">Renewal Option:</span>
                            <span
                                class="px-1 py-0.5 text-xs font-medium rounded 
                            {{ $record->renewal_option ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $record->renewal_option ? 'Available' : 'Not Available' }}
                            </span>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Terms and Conditions Section -->
            <section>
                <h2
                    class="text-xs font-bold text-gray-800 mb-1 border-b border-gray-300 pb-1 flex items-center section-accent">
                    TERMS & CONDITIONS
                </h2>

                <div class="bg-gray-50 p-2 rounded-sm border border-gray-200">
                    <div class="text-md text-gray-800 leading-tight prose prose-xs max-w-none">
                        @if ($record->terms_and_conditions)
                            {!! $record->terms_and_conditions !!}
                        @else
                            <!-- Default Terms -->
                            <h3 class="text-sm font-bold mb-2">Standard Lease Terms</h3>
                            <ol class="space-y-1">
                                <li>The tenant agrees to pay rent <strong>{{ $record->payment_frequency }}</strong> as
                                    specified in the financial terms above.</li>
                                <li>The tenant shall use the premises <strong>solely for residential purposes</strong>
                                    and shall not conduct any business activities without prior written consent from the
                                    landlord.</li>
                                <li>The tenant shall <strong>maintain the premises in good condition</strong> and shall
                                    be responsible for any damages beyond normal wear and tear.</li>
                                <li>The tenant shall <strong>not sublease, assign, or transfer</strong> any rights under
                                    this agreement without written consent from the landlord.</li>
                                <li>The tenant shall <strong>comply with all applicable laws, regulations, and community
                                        rules</strong> and shall not engage in any illegal activities on the premises.
                                </li>
                                <li>The landlord shall <strong>maintain the structural integrity</strong> of the
                                    property and ensure all major systems (plumbing, electrical, etc.) are in working
                                    order.</li>
                                <li>Either party may <strong>terminate this agreement with 30 days written
                                        notice</strong>, subject to applicable local laws and regulations.</li>
                                <li>
                                    @if ($record->renewal_option)
                                        This lease <strong>may be renewed upon mutual agreement</strong> of both parties
                                        before the expiration date.
                                    @else
                                        This lease <strong>shall not be automatically renewed</strong> and will
                                        terminate on the specified end date.
                                    @endif
                                </li>
                            </ol>
                        @endif
                    </div>
                </div>
            </section>

            @if ($record->notes)
                <!-- Additional Notes Section -->
                <section>
                    <h2
                        class="text-xs font-bold text-gray-800 mb-1 border-b border-gray-300 pb-1 flex items-center section-accent">
                        ADDITIONAL NOTES
                    </h2>

                    <div class="bg-gray-50 p-2 rounded-sm border border-gray-200">
                        <p class="text-xs text-gray-800 leading-tight">{{ $record->notes }}</p>
                    </div>
                </section>
            @endif

            <!-- Signatures Section -->
            <section>
                <h2
                    class="text-xs font-bold text-gray-800 mb-1 border-b border-gray-300 pb-1 flex items-center section-accent">
                    AGREEMENT SIGNATURES
                </h2>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Landlord Signature -->
                    <div class="bg-gray-50 p-2 rounded-sm border border-gray-200 text-center">
                        <h3 class="text-xs font-bold text-gray-700 mb-1 flex items-center justify-center dot-accent">
                            LANDLORD SIGNATURE
                        </h3>
                        <div class="signature-line"></div>
                        <div class="space-y-0">
                            <p class="text-xs font-semibold text-gray-800">
                                {{ $record->landlord->name ?? '[Landlord Name]' }}</p>
                            <p class="text-xs text-gray-600">
                                @if ($record->signed_date)
                                    Date: {{ $record->signed_date->format('M j, Y') }}
                                @else
                                    Date: _______________
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Tenant Signature -->
                    <div class="bg-gray-50 p-2 rounded-sm border border-gray-200 text-center">
                        <h3 class="text-xs font-bold text-gray-700 mb-1 flex items-center justify-center dot-accent">
                            TENANT SIGNATURE
                        </h3>
                        <div class="signature-line"></div>
                        <div class="space-y-0">
                            <p class="text-xs font-semibold text-gray-800">{{ $record->tenant->name ?? '[Tenant Name]' }}
                            </p>
                            <p class="text-xs text-gray-600">
                                @if ($record->signed_date)
                                    Date: {{ $record->signed_date->format('M j, Y') }}
                                @else
                                    Date: _______________
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <div class="bg-linear-to-r from-gray-100 to-gray-200 border-t border-gray-300 p-3 text-center">
            <p class="text-xs text-gray-600 font-medium mb-1">
                Document generated on {{ now()->format('F j, Y \\a\\t g:i A') }}
            </p>
            @php
                $businessName = config('app.name', 'HomeBaze Property Management System');

                // Try to get agency info from property
                if ($record->property && $record->property->agency) {
                    $businessName = $record->property->agency->name;
                } elseif ($record->property && $record->property->agent && $record->property->agent->agency) {
                    $businessName = $record->property->agent->agency->name;
                }
            @endphp

            <p class="text-xs text-gray-500">
                via {{ $businessName }}
            </p>
        </div>
    </div>
</body>

</html>
