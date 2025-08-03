@if(isset($isPdfMode) && $isPdfMode)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Agreement</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
@else
<x-filament-panels::page>
@endif
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4">
        <div class="max-w-5xl mx-auto">
            
            @if(!isset($isPdfMode) || !$isPdfMode)
            <!-- Template Selection Section -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Template Selection</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose a template to format the lease document or use default formatting</p>
                
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <label for="template-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Choose Template:</label>
                        <select id="template-select" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200" onchange="updateLinks()">
                            <option value="">Default Template</option>
                            @php
                                $templates = \App\Models\LeaseTemplate::where('landlord_id', auth()->id())
                                    ->where('is_active', true)
                                    ->get();
                            @endphp
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-4">
                    <a id="view-link" href="{{ route('landlord.lease.view-with-template', ['lease' => $record->id]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View with Template
                    </a>
                    <a id="download-link" href="{{ route('landlord.lease.download-pdf', ['lease' => $record->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
            @endif
            <!-- Document Container -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 dark:from-gray-700 dark:to-gray-800 text-white p-8 text-center">
                    <h1 class="text-xl md:text-2xl font-bold mb-3 tracking-wide">TENANCY AGREEMENT</h1>
                    <div class="w-24 h-0.5 bg-white/60 mx-auto mb-3"></div>
                    <p class="text-sm text-white/80 font-medium">Republic of Nigeria</p>
                </div>

                <!-- Main Content -->
                <div class="p-6 space-y-5">
                    
                    <!-- Parties Section -->
                    <section>
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2 flex items-center">
                            <div class="w-1 h-4 bg-gray-600 mr-2"></div>
                            PARTIES TO THE AGREEMENT
                        </h2>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Landlord -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-4 rounded border border-gray-200 dark:border-gray-600">
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <div class="w-1.5 h-1.5 bg-gray-600 rounded-full mr-1.5"></div>
                                    LANDLORD
                                </h3>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">{{ $record->landlord->name ?? 'Landlord Name' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $record->landlord->email ?? 'landlord@example.com' }}</p>
                            </div>

                            <!-- Tenant -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-4 rounded border border-gray-200 dark:border-gray-600">
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <div class="w-1.5 h-1.5 bg-gray-600 rounded-full mr-1.5"></div>
                                    TENANT
                                </h3>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">{{ $record->tenant->name ?? 'Tenant Name' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $record->tenant->email ?? 'tenant@example.com' }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Property Details Section -->
                    <section>
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2 flex items-center">
                            <div class="w-1 h-4 bg-gray-600 mr-2"></div>
                            PROPERTY DETAILS
                        </h2>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-4 rounded border border-gray-200 dark:border-gray-600">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 w-20 flex-shrink-0">Property:</span>
                                        <span class="text-xs text-gray-800 dark:text-gray-200 font-medium">{{ $record->property->title ?? 'Property Title' }}</span>
                                    </div>
                                    @if($record->property->address)
                                    <div class="flex items-start">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 w-20 flex-shrink-0">Address:</span>
                                        <span class="text-xs text-gray-800 dark:text-gray-200">{{ $record->property->address }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 w-20 flex-shrink-0">Type:</span>
                                        <span class="text-xs text-gray-800 dark:text-gray-200">{{ $record->property->propertyType->name ?? 'N/A' }}</span>
                                    </div>
                                    @if($record->property->propertySubtype)
                                    <div class="flex items-start">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 w-20 flex-shrink-0">Subtype:</span>
                                        <span class="text-xs text-gray-800 dark:text-gray-200">{{ $record->property->propertySubtype->name }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Lease Terms Section -->
                    <section>
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2 flex items-center">
                            <div class="w-1 h-4 bg-gray-600 mr-2"></div>
                            LEASE TERMS & FINANCIAL DETAILS
                        </h2>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-3 rounded border border-gray-200 dark:border-gray-600">
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 text-xs">
                                <!-- Start Date -->
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">Start Date</span>
                                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $record->start_date->format('M j, Y') }}</p>
                                </div>
                                
                                <!-- End Date -->
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">End Date</span>
                                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $record->end_date->format('M j, Y') }}</p>
                                </div>
                                
                                <!-- Duration -->
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">Duration</span>
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $record->start_date->diffInMonths($record->end_date) }} Months</p>
                                </div>
                                
                                <!-- Annual Rent -->
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">Annual Rent</span>
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200">₦{{ number_format($record->monthly_rent, 0) }}</p>
                                </div>
                                
                                <!-- Payment Terms -->
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">Payment Terms</span>
                                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ ucfirst($record->payment_frequency) }}</p>
                                    @if($record->payment_frequency === 'biannually')
                                    <p class="text-xs text-gray-600 dark:text-gray-400">(₦{{ number_format($record->monthly_rent / 2, 0) }})</p>
                                    @elseif($record->payment_frequency === 'quarterly')
                                    <p class="text-xs text-gray-600 dark:text-gray-400">(₦{{ number_format($record->monthly_rent / 4, 0) }})</p>
                                    @endif
                                </div>
                                
                                <!-- Status -->
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 block mb-1">Status</span>
                                    <span class="inline-block px-1.5 py-0.5 text-xs font-medium rounded 
                                        @if($record->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                        @elseif($record->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300
                                        @elseif($record->status === 'expired') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 @endif">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                    @if($record->signed_date)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $record->signed_date->format('M j, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($record->renewal_option !== null)
                            <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 text-center">
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 mr-1">Renewal Option:</span>
                                <span class="px-1.5 py-0.5 text-xs font-medium rounded 
                                    {{ $record->renewal_option ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ $record->renewal_option ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </section>

                    <!-- Terms and Conditions Section -->
                    <section>
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2 flex items-center">
                            <div class="w-1 h-4 bg-gray-600 mr-2"></div>
                            TERMS & CONDITIONS
                        </h2>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-4 rounded border border-gray-200 dark:border-gray-600">
                            @if($record->terms_and_conditions)
                                <div class="prose prose-sm max-w-none text-gray-800 dark:text-gray-200">
                                    {!! $record->terms_and_conditions !!}
                                </div>
                            @else
                                <!-- Default Terms -->
                                <ol class="space-y-2 text-xs text-gray-800 dark:text-gray-200 leading-relaxed">
                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">1</span>
                                        <span class="text-xs">The tenant agrees to pay rent <strong>{{ $record->payment_frequency }}</strong> as specified in the financial terms above.</span>
                                    </li>

                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">2</span>
                                        <span class="text-xs">The tenant shall use the premises <strong>solely for residential purposes</strong> and shall not conduct any business activities without prior written consent from the landlord.</span>
                                    </li>

                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">3</span>
                                        <span class="text-xs">The tenant shall <strong>maintain the premises in good condition</strong> and shall be responsible for any damages beyond normal wear and tear.</span>
                                    </li>

                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">4</span>
                                        <span class="text-xs">The tenant shall <strong>not sublease, assign, or transfer</strong> any rights under this agreement without written consent from the landlord.</span>
                                    </li>

                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">5</span>
                                        <span class="text-xs">The tenant shall <strong>comply with all applicable laws, regulations, and community rules</strong> and shall not engage in any illegal activities on the premises.</span>
                                    </li>

                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">6</span>
                                        <span class="text-xs">The landlord shall <strong>maintain the structural integrity</strong> of the property and ensure all major systems (plumbing, electrical, etc.) are in working order.</span>
                                    </li>

                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">7</span>
                                        <span class="text-xs">Either party may <strong>terminate this agreement with 30 days written notice</strong>, subject to applicable local laws and regulations.</span>
                                    </li>

                                    <li class="flex items-start bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 bg-gray-600 text-white text-xs font-bold rounded-full flex items-center justify-center mr-3 mt-0.5">8</span>
                                        <span class="text-xs">
                                            @if($record->renewal_option)
                                                This lease <strong>may be renewed upon mutual agreement</strong> of both parties before the expiration date.
                                            @else
                                                This lease <strong>shall not be automatically renewed</strong> and will terminate on the specified end date.
                                            @endif
                                        </span>
                                    </li>
                                </ol>
                            @endif
                        </div>
                    </section>

                    @if($record->notes)
                    <!-- Additional Notes Section -->
                    <section>
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2 flex items-center">
                            <div class="w-1 h-4 bg-gray-600 mr-2"></div>
                            ADDITIONAL NOTES
                        </h2>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-4 rounded border border-gray-200 dark:border-gray-600">
                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap">{{ $record->notes }}</p>
                        </div>
                    </section>
                    @endif

                    <!-- Signatures Section -->
                    <section>
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-600 pb-2 flex items-center">
                            <div class="w-1 h-4 bg-gray-600 mr-2"></div>
                            AGREEMENT SIGNATURES
                        </h2>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Landlord Signature -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-4 rounded border border-gray-200 dark:border-gray-600">
                                <div class="text-center">
                                    <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 bg-gray-600 rounded-full mr-1.5"></div>
                                        LANDLORD SIGNATURE
                                    </h3>
                                    <div class="h-12 border-b border-gray-400 dark:border-gray-500 mb-3 relative">
                                        <span class="absolute bottom-0 right-1 text-xs text-gray-500 dark:text-gray-400">Signature</span>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $record->landlord->name }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            @if($record->signed_date)
                                                Date: {{ $record->signed_date->format('M j, Y') }}
                                            @else
                                                Date: _______________
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tenant Signature -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-750 p-4 rounded border border-gray-200 dark:border-gray-600">
                                <div class="text-center">
                                    <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 bg-gray-600 rounded-full mr-1.5"></div>
                                        TENANT SIGNATURE
                                    </h3>
                                    <div class="h-12 border-b border-gray-400 dark:border-gray-500 mb-3 relative">
                                        <span class="absolute bottom-0 right-1 text-xs text-gray-500 dark:text-gray-400">Signature</span>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $record->tenant->name }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            @if($record->signed_date)
                                                Date: {{ $record->signed_date->format('M j, Y') }}
                                            @else
                                                Date: _______________
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Footer -->
                <div class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 border-t border-gray-300 dark:border-gray-600 p-6">
                    <div class="text-center">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                            Document generated on {{ now()->format('F j, Y \a\t g:i A') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            via HomeBaze Property Management System
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if(!isset($isPdfMode) || !$isPdfMode)
            <div class="flex justify-center space-x-4 mt-8 no-print">
                <button onclick="window.print()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Print Agreement
                </button>
                <button onclick="window.history.back()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Go Back
                </button>
            </div>
            @endif
        </div>
    </div>

    <style>
        @if(isset($isPdfMode) && $isPdfMode)
        /* PDF-specific styles */
        body {
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            print-color-adjust: exact;
            background: white !important;
        }
        
        .bg-gradient-to-br,
        .bg-gradient-to-r {
            background: white !important;
            border: 1px solid #d1d5db !important;
        }
        
        .text-white {
            color: #1f2937 !important;
        }
        
        .shadow-xl,
        .shadow-lg {
            box-shadow: none !important;
        }
        @endif

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            .bg-gray-50 {
                background: white !important;
            }
            .dark\:bg-gray-800,
            .dark\:bg-gray-700 {
                background: white !important;
                border: 1px solid #d1d5db !important;
            }
            .dark\:text-gray-100,
            .dark\:text-gray-200,
            .dark\:text-gray-300 {
                color: #374151 !important;
            }
        }

        @media (max-width: 768px) {
            .text-2xl { font-size: 1.5rem; }
            .text-lg { font-size: 1.125rem; }
            .text-base { font-size: 1rem; }
            .text-sm { font-size: 0.875rem; }
        }

        @media (max-width: 640px) {
            .text-3xl { font-size: 1.5rem; }
            .text-2xl { font-size: 1.25rem; }
            .text-lg { font-size: 1rem; }
        }
        
        function updateLinks() {
            const select = document.getElementById('template-select');
            const templateId = select.value;
            const leaseId = {{ $record->id }};
            
            const viewLink = document.getElementById('view-link');
            const downloadLink = document.getElementById('download-link');
            
            if (templateId) {
                viewLink.href = `/landlord/lease/${leaseId}/view-with-template?template=${templateId}`;
                downloadLink.href = `/landlord/lease/${leaseId}/download-pdf?template=${templateId}`;
            } else {
                viewLink.href = `/landlord/lease/${leaseId}/view-with-template`;
                downloadLink.href = `/landlord/lease/${leaseId}/download-pdf`;
            }
        }
    </style>
@if(isset($isPdfMode) && $isPdfMode)
</body>
</html>
@else
</x-filament-panels::page>
@endif