{{-- resources/views/lease-components/sections/signature.blade.php --}}
<div class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="text-center mb-6">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Agreement Execution</h3>
        <div class="w-24 h-0.5 bg-gray-300 mx-auto"></div>
    </div>
    
    {{-- Signature Areas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
        <div class="text-center space-y-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="space-y-1 mb-4">
                    <div class="font-semibold text-blue-800 dark:text-blue-200 uppercase tracking-wide">Landlord (Lessor)</div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">{{ $landlord->name }}</div>
                    @if($landlord->phone_number)
                        <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $landlord->phone_number }}</div>
                    @endif
                </div>
                <div class="border-b-2 border-blue-300 dark:border-blue-700 mb-3 mx-4 h-12"></div>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div class="font-medium">Signature</div>
                    <div>Date: ____________________</div>
                </div>
            </div>
        </div>
        
        <div class="text-center space-y-4">
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="space-y-1 mb-4">
                    <div class="font-semibold text-green-800 dark:text-green-200 uppercase tracking-wide">Tenant (Lessee)</div>
                    <div class="text-gray-700 dark:text-gray-300 font-medium">{{ $tenant->name }}</div>
                    @if($tenant->phone_number)
                        <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $tenant->phone_number }}</div>
                    @endif
                </div>
                <div class="border-b-2 border-green-300 dark:border-green-700 mb-3 mx-4 h-12"></div>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div class="font-medium">Signature</div>
                    <div>Date: ____________________</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Witness Section (Optional) --}}
    @if($config['show_witness'] ?? false)
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
            <h4 class="text-center text-lg font-medium text-gray-800 dark:text-gray-200 mb-6">Witnesses</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="text-center space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="space-y-1 mb-4">
                            <div class="font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wide">Witness 1</div>
                            <div class="text-gray-600 dark:text-gray-400">Name: ________________________</div>
                        </div>
                        <div class="border-b-2 border-gray-300 dark:border-gray-600 mb-3 mx-4 h-12"></div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <div class="font-medium">Signature</div>
                            <div>Date: ____________________</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="space-y-1 mb-4">
                            <div class="font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wide">Witness 2</div>
                            <div class="text-gray-600 dark:text-gray-400">Name: ________________________</div>
                        </div>
                        <div class="border-b-2 border-gray-300 dark:border-gray-600 mb-3 mx-4 h-12"></div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <div class="font-medium">Signature</div>
                            <div>Date: ____________________</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Legal Notice --}}
    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
        <div class="text-sm text-yellow-800 dark:text-yellow-200">
            <div class="font-semibold mb-2 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Legal Notice
            </div>
            <div class="leading-relaxed">
                By signing this agreement, both parties acknowledge they have read, understood, and agree to be bound by all terms and conditions stated herein. This agreement shall be governed by the laws of Nigeria.
            </div>
        </div>
    </div>
</div>