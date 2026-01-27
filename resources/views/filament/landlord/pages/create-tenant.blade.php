<x-filament-panels::page>
    <div x-data="{ 
            step: @entangle('step'), 
            totalSteps: 3,
            next() { $wire.nextStep() },
            prev() { $wire.previousStep() }
         }" 
         class="relative min-h-screen flex flex-col pb-20"
    >
        {{-- Wizard Interface --}}
        <div class="flex-1 flex flex-col">
            {{-- Top Navigation --}}
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('filament.landlord.pages.tenants-list') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 flex items-center">
                    <x-heroicon-s-arrow-left class="w-4 h-4 mr-1"/> Back
                </a>
                <div class="flex space-x-1" x-show="step < 4">
                    <template x-for="i in totalSteps">
                        <div class="h-1.5 w-6 rounded-full transition-colors duration-300"
                             :class="i <= step ? 'bg-primary-600 dark:bg-primary-500' : 'bg-gray-200 dark:bg-gray-700'"></div>
                    </template>
                </div>
            </div>

            {{-- Steps Container --}}
            <div class="flex-1 relative overflow-hidden px-2">
                
                {{-- Step 1: Personal Information --}}
                <div x-show="step === 1"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-full"
                     class="absolute inset-0 flex flex-col space-y-6"
                >
                    <div class="text-center space-y-2">
                        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400">
                            Personal Information
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Enter the tenant's basic details</p>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name *</label>
                                <input type="text" wire:model="first_name" placeholder="John" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                @error('first_name') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name *</label>
                                <input type="text" wire:model="last_name" placeholder="Doe" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                @error('last_name') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input type="email" wire:model="email" placeholder="john@example.com" 
                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                            @error('email') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                            <input type="tel" wire:model="phone" placeholder="+234 800 000 0000" 
                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                            @error('phone') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Birth</label>
                                <input type="date" wire:model="date_of_birth" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nationality</label>
                                <input type="text" wire:model="nationality" placeholder="Nigerian" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button @click="next()" type="button" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95 text-center">
                            Continue
                        </button>
                    </div>
                </div>

                {{-- Step 2: Employment Information --}}
                <div x-show="step === 2" style="display: none;"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-full"
                     class="absolute inset-0 flex flex-col space-y-6"
                >
                    <div class="text-center space-y-2">
                        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400">
                            Employment Details
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Financial background helps assess tenancy</p>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employment Status</label>
                            <select wire:model="employment_status" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                <option value="">Select Status</option>
                                <option value="employed">Employed</option>
                                <option value="self_employed">Self Employed</option>
                                <option value="unemployed">Unemployed</option>
                                <option value="retired">Retired</option>
                                <option value="student">Student</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employer Name</label>
                            <input type="text" wire:model="employer_name" placeholder="Company name" 
                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Occupation</label>
                            <input type="text" wire:model="occupation" placeholder="Job title" 
                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monthly Income (₦)</label>
                            <input type="number" wire:model="monthly_income" placeholder="0.00" 
                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                        </div>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Back</button>
                        <button @click="next()" type="button" class="flex-1 py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95">
                            Continue
                        </button>
                    </div>
                </div>

                {{-- Step 3: Identification & Emergency --}}
                <div x-show="step === 3" style="display: none;"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-full"
                     class="absolute inset-0 flex flex-col space-y-6 overflow-y-auto pb-32"
                >
                    <div class="text-center space-y-2">
                        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400">
                            Final Details
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Identification & emergency contacts</p>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID Type</label>
                                <select wire:model="identification_type" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                    <option value="">Select Type</option>
                                    <option value="national_id">National ID</option>
                                    <option value="international_passport">International Passport</option>
                                    <option value="drivers_license">Driver's License</option>
                                    <option value="voters_card">Voter's Card</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID Number</label>
                                <input type="text" wire:model="identification_number" placeholder="ID number" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Emergency Contact</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <input type="text" wire:model="emergency_contact_name" placeholder="Contact name" 
                                           class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                </div>
                                <div>
                                    <input type="tel" wire:model="emergency_contact_phone" placeholder="Phone" 
                                           class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Guarantor (Optional)</h3>
                            <div class="space-y-3">
                                <input type="text" wire:model="guarantor_name" placeholder="Guarantor name" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="tel" wire:model="guarantor_phone" placeholder="Phone" 
                                           class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                    <input type="email" wire:model="guarantor_email" placeholder="Email" 
                                           class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                            <textarea wire:model="notes" rows="2" placeholder="Any additional notes..." 
                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none"></textarea>
                        </div>
                    </div>

                    <div class="flex space-x-3 pt-4 pb-10">
                        <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Back</button>
                        <button wire:click="create" wire:loading.attr="disabled" type="button" class="flex-1 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium shadow-lg shadow-green-500/30 transition-all active:scale-95 flex items-center justify-center space-x-2">
                            <span wire:loading.remove>Add Tenant</span>
                            <span wire:loading>Adding...</span>
                        </button>
                    </div>
                </div>

                {{-- Step 4: Success --}}
                <div x-show="step === 4" style="display: none;"
                     x-transition:enter="transition ease-out duration-500 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute inset-0 flex flex-col items-center justify-center space-y-8 text-center"
                >
                    <div class="w-24 h-24 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4 animate-bounce">
                        <x-heroicon-o-check-circle class="w-16 h-16 text-green-600 dark:text-green-400" />
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Tenant Added!</h2>
                        <p class="text-gray-500 dark:text-gray-400">{{ $createdTenantName ?? 'New tenant' }} is now in your tenant list.</p>
                    </div>

                    <div class="w-full max-w-xs space-y-3 pt-6">
                        <button wire:click="backToTenants" type="button" 
                            class="block w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95">
                            View Tenants
                        </button>

                        <button wire:click="createAnother" type="button" class="w-full py-3.5 bg-white border border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Add Another Tenant
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-filament-panels::page>
