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
            class="bg-gradient-to-r from-slate-50 to-slate-100 p-8 shadow-2xl rounded-lg border border-gray-200 max-w-7xl mx-auto relative overflow-hidden">
            <!-- Premium subtle background pattern -->
            <div class="absolute inset-0 opacity-5 pattern-diagonal-lines pattern-gray-700 pattern-size-2 pattern-bg-transparent"></div>

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
                    <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="2"></circle>
                    <circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-width="0.3"></circle>
                    <circle cx="50" cy="50" r="43" fill="none" stroke="currentColor" stroke-width="0.2" stroke-dasharray="1,2"></circle>
                    
                    <!-- Middle decorative elements -->
                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="1"></circle>
                    <circle cx="50" cy="50" r="36" fill="none" stroke="currentColor" stroke-width="0.5" stroke-dasharray="3,2"></circle>
                    <circle cx="50" cy="50" r="34" fill="none" stroke="currentColor" stroke-width="0.3" stroke-dasharray="1,1"></circle>
                    
                    <!-- Unique pattern elements - waves -->
                    <path d="M30,50 Q40,45 50,50 Q60,55 70,50" fill="none" stroke="currentColor" stroke-width="0.3"></path>
                    <path d="M30,52 Q40,57 50,52 Q60,47 70,52" fill="none" stroke="currentColor" stroke-width="0.3"></path>
                    
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
                        <line x1="50" y1="10" x2="50" y2="22" stroke-width="0.6"></line>
                        <line x1="50" y1="78" x2="50" y2="90" stroke-width="0.6"></line>
                        <line x1="10" y1="50" x2="22" y2="50" stroke-width="0.6"></line>
                        <line x1="78" y1="50" x2="90" y2="50" stroke-width="0.6"></line>
                        
                        <!-- Diagonal lines with varied lengths -->
                        <line x1="25" y1="25" x2="34" y2="34" stroke-width="0.4"></line>
                        <line x1="75" y1="25" x2="66" y2="34" stroke-width="0.4"></line>
                        <line x1="25" y1="75" x2="34" y2="66" stroke-width="0.4"></line>
                        <line x1="75" y1="75" x2="66" y2="66" stroke-width="0.4"></line>
                        
                        <!-- Additional diagonal security lines -->
                        <line x1="36" y1="25" x2="40" y2="29" stroke-width="0.2"></line>
                        <line x1="64" y1="25" x2="60" y2="29" stroke-width="0.2"></line>
                        <line x1="36" y1="75" x2="40" y2="71" stroke-width="0.2"></line>
                        <line x1="64" y1="75" x2="60" y2="71" stroke-width="0.2"></line>
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center mb-6 relative z-10">
                <!-- Company Info -->
                <div class="flex items-center space-x-4">
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
                            $businessLogo = $agency->logo ? asset('storage/' . $agency->logo) : null;
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
                                'website' => $owner->website ?? null,
                                'tagline' => null // No tagline for property owner companies
                            ];
                            $businessInitials = strtoupper(substr($owner->company_name, 0, 2));
                        }
                        // 3. Check for Independent Agent's business (if property has independent agent)
                        elseif ($receipt->lease && $receipt->lease->property && $receipt->lease->property->agent_id &&
                                !$receipt->lease->property->agency_id) {
                            // Independent agent - for now use agent's name as business name
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
                                'tagline' => 'Management System'
                            ];
                            $businessLogo = asset('img/homebaze_logo.png');
                        }
                    @endphp

                    @if ($businessLogo)
                        <img src="{{ $businessLogo }}" alt="{{ $businessInfo['name'] }} Logo" class="w-20 drop-shadow-md">
                    @else
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center">
                            <span class="text-lg font-bold text-indigo-600">{{ $businessInitials }}</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">{{ $businessInfo['name'] }}</h2>
                        @if($businessInfo['tagline'])
                            <p class="text-sm text-gray-600">{{ $businessInfo['tagline'] }}</p>
                        @endif
                    </div>
                </div>

                <!-- Contact Details -->
                <div class="text-center text-gray-600 text-sm">
                    <p class="font-semibold">{{ $businessInfo['email'] }}</p>
                    @if($businessInfo['website'])
                        <p>{{ $businessInfo['website'] }}</p>
                    @endif
                    <p class="text-xs">{{ $businessInfo['phone'] }}</p>
                </div>

                <!-- Receipt Number -->
                <div class="text-right">
                    <div class="bg-indigo-100 px-3 py-2 rounded shadow-sm border border-indigo-200">
                        <p class="text-xs text-gray-600">Receipt No:</p>
                        <p class="text-sm font-bold text-indigo-700">{{ $receipt->receipt_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Row - Split into Left and Right -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Tenant & Date Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <p class="text-sm font-semibold text-gray-600 mb-1">Received From:</p>
                            <p class="text-lg text-gray-800">{{ $receipt->tenant->name ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <p class="text-sm font-semibold text-gray-600 mb-1">Payment Date:</p>
                            <p class="text-lg text-gray-800">{{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('F j, Y') : now()->format('F j, Y') }}</p>
                        </div>
                    </div>

                    <!-- Property Information -->
                    @if($receipt->lease && $receipt->lease->property)
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500 shadow-sm">
                        <p class="font-semibold text-gray-700 mb-2">Property Details</p>
                        <p class="text-gray-800 font-medium">{{ $receipt->lease->property->title }}</p>
                        @if($receipt->lease->property->address)
                            <p class="text-sm text-gray-600 mt-1">{{ $receipt->lease->property->address }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Lease Information -->
                    @if($receipt->lease)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500 shadow-sm">
                            <p class="font-semibold text-gray-700">Lease Start:</p>
                            <p class="text-gray-800">{{ $receipt->lease->start_date ? \Carbon\Carbon::parse($receipt->lease->start_date)->format('F j, Y') : 'N/A' }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500 shadow-sm">
                            <p class="font-semibold text-gray-700">Lease End:</p>
                            <p class="text-gray-800">{{ $receipt->lease->end_date ? \Carbon\Carbon::parse($receipt->lease->end_date)->format('F j, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Amount Section -->
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-lg shadow-lg border-2 border-indigo-100">
                        <p class="text-lg font-semibold text-gray-700 mb-2">Total Amount</p>
                        <p class="text-3xl font-bold text-indigo-700">₦{{ number_format($receipt->amount, 2) }}</p>
                        <div class="mt-4 p-3 bg-white/70 rounded">
                            <p class="text-sm font-semibold text-gray-600">Amount in Words:</p>
                            <p class="text-gray-800 italic">{{ $amountInWords }}</p>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <p class="font-semibold text-gray-700 mb-3">Payment Information</p>
                        <div class="space-y-2">
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

            <!-- Bottom Row - Payment Methods & Signature -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-end">
                <!-- Payment Methods -->
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="font-semibold text-gray-700 mb-3">Payment Method</p>
                    <div class="flex gap-2">
                        <div class="flex items-center space-x-1 px-2 py-1 rounded border text-xs {{ $receipt->payment_method == 'cash' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                            <div class="w-4 h-4 border-2 border-gray-500 rounded-sm flex items-center justify-center {{ $receipt->payment_method == 'cash' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if($receipt->payment_method == 'cash')
                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                @endif
                            </div>
                            <span>Cash</span>
                        </div>
                        <div class="flex items-center space-x-1 px-2 py-1 rounded border text-xs {{ $receipt->payment_method == 'transfer' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                            <div class="w-4 h-4 border-2 border-gray-500 rounded-sm flex items-center justify-center {{ $receipt->payment_method == 'transfer' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if($receipt->payment_method == 'transfer')
                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                @endif
                            </div>
                            <span>Transfer</span>
                        </div>
                        <div class="flex items-center space-x-1 px-2 py-1 rounded border text-xs {{ $receipt->payment_method == 'pos' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                            <div class="w-4 h-4 border-2 border-gray-500 rounded-sm flex items-center justify-center {{ $receipt->payment_method == 'pos' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if($receipt->payment_method == 'pos')
                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                @endif
                            </div>
                            <span>POS</span>
                        </div>
                        <div class="flex items-center space-x-1 px-2 py-1 rounded border text-xs {{ $receipt->payment_method == 'card' ? 'bg-indigo-100 border-indigo-500' : 'border-gray-300' }}">
                            <div class="w-4 h-4 border-2 border-gray-500 rounded-sm flex items-center justify-center {{ $receipt->payment_method == 'card' ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                @if($receipt->payment_method == 'card')
                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                @endif
                            </div>
                            <span>Card</span>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="flex justify-end">
                    <div class="text-center">
                        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-300 mb-3 inline-block">
                            @php
                                $qrCode = $this->generateQrCode();
                            @endphp
                            @if($qrCode)
                                {!! $qrCode !!}
                            @else
                                <svg width="100" height="100" viewBox="0 0 100 100" class="border">
                                    <rect width="100" height="100" fill="white"/>
                                    <g fill="black">
                                        <rect x="10" y="10" width="20" height="20"/>
                                        <rect x="70" y="10" width="20" height="20"/>
                                        <rect x="10" y="70" width="20" height="20"/>
                                        <rect x="40" y="40" width="20" height="20"/>
                                    </g>
                                    <text x="50" y="55" text-anchor="middle" font-size="8" fill="black">QR</text>
                                </svg>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 font-medium">Scan to verify receipt</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 pt-4 border-t border-gray-200 text-center text-gray-500 text-sm">
                <p>Thank you for your timely payment!</p>
                <p class="mt-1">This receipt was generated electronically and is valid without a physical signature.</p>
                <p class="mt-1 font-semibold">Generated via HomeBaze Property Management System | <strong>Powered by DevCentric</strong></p>
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