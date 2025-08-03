{{-- resources/views/pdf-lease-components/signature.blade.php --}}
<div class="mt-6 break-inside-avoid">
    <div class="border-b border-gray-300 pb-1 mb-3">
        <h3 class="text-center text-xs font-semibold text-gray-900 uppercase tracking-wide">Agreement Execution</h3>
    </div>
    
    {{-- Signature Lines --}}
    <div class="grid grid-cols-2 gap-6 mb-3">
        <div class="text-center">
            <div class="text-xs space-y-0.5 mb-8">
                <div class="font-semibold text-gray-800 uppercase tracking-wide text-xs">Landlord (Lessor)</div>
                <div class="text-gray-600 font-medium text-xs">{{ $landlord->name }}</div>
                @if($landlord->phone_number)
                    <div class="text-gray-500 text-xs">{{ $landlord->phone_number }}</div>
                @endif
            </div>
            {{-- Signature and Date in Flexbox Row --}}
            <div class="flex items-center gap-4 mb-1">
                <div class="flex items-center flex-1">
                    <span class="text-xs text-gray-500 mr-2">Signature:</span>
                    <div class="border-b border-gray-400 flex-1"></div>
                </div>
                <div class="flex items-center flex-1">
                    <span class="text-xs text-gray-500 mr-2">Date:</span>
                    <div class="border-b border-gray-400 flex-1"></div>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <div class="text-xs space-y-0.5 mb-8">
                <div class="font-semibold text-gray-800 uppercase tracking-wide text-xs">Tenant (Lessee)</div>
                <div class="text-gray-600 font-medium text-xs">{{ $tenant->name }}</div>
                @if($tenant->phone_number)
                    <div class="text-gray-500 text-xs">{{ $tenant->phone_number }}</div>
                @endif
            </div>
            {{-- Signature and Date in Flexbox Row --}}
            <div class="flex items-center gap-4 mb-1">
                <div class="flex items-center flex-1">
                    <span class="text-xs text-gray-500 mr-2">Signature:</span>
                    <div class="border-b border-gray-400 flex-1"></div>
                </div>
                <div class="flex items-center flex-1">
                    <span class="text-xs text-gray-500 mr-2">Date:</span>
                    <div class="border-b border-gray-400 flex-1"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Witness Section (Optional) --}}
    @if($config['show_witness'] ?? false)
        <div class="mt-4 pt-2 border-t border-gray-200">
            <div class="grid grid-cols-2 gap-6">
                <div class="text-center">
                    <div class="text-xs space-y-0.5 mb-4">
                        <div class="font-semibold text-gray-800 uppercase tracking-wide text-xs">Witness 1</div>
                        <div class="text-gray-600 text-xs">Name: ________________________</div>
                    </div>
                    {{-- Signature and Date in Flexbox Row --}}
                    <div class="flex items-center gap-4 mb-1">
                        <div class="flex items-center flex-1">
                            <span class="text-xs text-gray-500 mr-2">Signature:</span>
                            <div class="border-b border-gray-400 flex-1"></div>
                        </div>
                        <div class="flex items-center flex-1">
                            <span class="text-xs text-gray-500 mr-2">Date:</span>
                            <div class="border-b border-gray-400 flex-1"></div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="text-xs space-y-0.5 mb-4">
                        <div class="font-semibold text-gray-800 uppercase tracking-wide text-xs">Witness 2</div>
                        <div class="text-gray-600 text-xs">Name: ________________________</div>
                    </div>
                    {{-- Signature and Date in Flexbox Row --}}
                    <div class="flex items-center gap-4 mb-1">
                        <div class="flex items-center flex-1">
                            <span class="text-xs text-gray-500 mr-2">Signature:</span>
                            <div class="border-b border-gray-400 flex-1"></div>
                        </div>
                        <div class="flex items-center flex-1">
                            <span class="text-xs text-gray-500 mr-2">Date:</span>
                            <div class="border-b border-gray-400 flex-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Legal Notice --}}
    <div class="mt-3 p-1.5 bg-gray-50 border border-gray-200 rounded-sm">
        <div class="text-xs text-gray-600 text-center">
            <div class="font-medium mb-0.5 text-xs">Legal Notice</div>
            <div class="leading-relaxed text-xs">
                By signing this agreement, both parties acknowledge they have read, understood, and agree to be bound by all terms and conditions stated herein. This agreement shall be governed by the laws of Nigeria.
            </div>
        </div>
    </div>
</div>